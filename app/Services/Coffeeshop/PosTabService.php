<?php

namespace App\Services\Coffeeshop;

use App\Models\ActivityLog;
use App\Models\PosProduct;
use App\Models\PosTab;
use App\Models\PosTabItem;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PosTabService
{
    public function __construct(
        private PosInventoryService $inventoryService
    ) {}

    public function openTab(array $data): PosTab
    {
        $tabType = $data['tab_type'] ?? 'walk_in';

        if ($tabType === 'room' && empty($data['folio_id'])) {
            throw new \InvalidArgumentException('Folio ID is required for room charge.');
        }

        if ($tabType === 'account' && empty($data['credit_account_id'])) {
            throw new \InvalidArgumentException('Credit Account ID is required for account charge.');
        }

        return PosTab::create([
            'tab_name' => $data['tab_name'],
            'tab_type' => $data['tab_type'] ?? 'walk_in',
            'guest_id' => $data['guest_id'] ?? null,
            'folio_id' => $data['folio_id'] ?? null,
            'credit_account_id' => $data['credit_account_id'] ?? null,
            'booking_id' => $data['booking_id'] ?? null,
            'room_id' => $data['room_id'] ?? null,
            'status' => 'open',
            'subtotal' => 0,
            'total' => 0,
            'opened_by' => auth()->id() ?? 1,
            'opened_at' => now(),
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function addItem(PosTab $tab, int $productId, int $quantity = 1): PosTab
    {
        if ($tab->status !== 'open') {
            throw new RuntimeException('Cannot add items to a closed or cancelled tab.');
        }

        $product = PosProduct::active()->findOrFail($productId);

        if ($product->stock_quantity < $quantity) {
            throw new RuntimeException("Insufficient stock for {$product->name}. Available: {$product->stock_quantity}.");
        }

        return DB::transaction(function () use ($tab, $product, $quantity) {
            $this->inventoryService->adjustStock($product, -$quantity, 'sale', 'pos_tab', $tab->tab_id, "Added to tab {$tab->tab_name}");

            $existing = PosTabItem::where('tab_id', $tab->tab_id)
                ->where('product_id', $product->product_id)
                ->first();

            if ($existing) {
                $newQty = $existing->quantity + $quantity;
                $existing->update([
                    'quantity' => $newQty,
                    'line_total' => $newQty * $existing->unit_price,
                ]);
            } else {
                PosTabItem::create([
                    'tab_id' => $tab->tab_id,
                    'product_id' => $product->product_id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'line_total' => $quantity * $product->price,
                ]);
            }

            $tab->recalculateTotals();

            return $tab->fresh(['items.product.category', 'room', 'guest', 'folio']);
        });
    }

    public function updateItemQuantity(PosTab $tab, PosTabItem $item, int $quantity): PosTab
    {
        if ($tab->status !== 'open') {
            throw new RuntimeException('Cannot modify a closed or cancelled tab.');
        }

        if ($item->tab_id !== $tab->tab_id) {
            throw new RuntimeException('Item does not belong to this tab.');
        }

        if ($quantity <= 0) {
            return $this->removeItem($tab, $item);
        }

        $product = PosProduct::findOrFail($item->product_id);
        $diff = $quantity - $item->quantity;

        return DB::transaction(function () use ($tab, $item, $quantity, $product, $diff) {
            if ($diff > 0) {
                if ($product->stock_quantity < $diff) {
                    throw new RuntimeException("Insufficient stock for {$product->name}. Available: {$product->stock_quantity}.");
                }
                $this->inventoryService->adjustStock($product, -$diff, 'sale', 'pos_tab', $tab->tab_id, 'Increased tab item quantity');
            } elseif ($diff < 0) {
                $this->inventoryService->adjustStock($product, abs($diff), 'cancel', 'pos_tab', $tab->tab_id, 'Decreased tab item quantity');
            }

            $item->update([
                'quantity' => $quantity,
                'line_total' => $quantity * $item->unit_price,
            ]);

            $tab->recalculateTotals();

            return $tab->fresh(['items.product.category', 'room', 'guest', 'folio']);
        });
    }

    public function removeItem(PosTab $tab, PosTabItem $item): PosTab
    {
        if ($tab->status !== 'open') {
            throw new RuntimeException('Cannot modify a closed or cancelled tab.');
        }

        $product = PosProduct::findOrFail($item->product_id);

        return DB::transaction(function () use ($tab, $item, $product) {
            $this->inventoryService->adjustStock($product, $item->quantity, 'cancel', 'pos_tab', $tab->tab_id, 'Removed tab item');

            $item->delete();
            $tab->recalculateTotals();

            return $tab->fresh(['items.product.category', 'room', 'guest', 'folio']);
        });
    }

    public function cancelTab(PosTab $tab): PosTab
    {
        if ($tab->status !== 'open') {
            throw new RuntimeException('Only open tabs can be cancelled.');
        }

        return DB::transaction(function () use ($tab) {
            $tab->load('items');
            foreach ($tab->items as $item) {
                $product = PosProduct::find($item->product_id);
                if ($product) {
                    $this->inventoryService->adjustStock($product, $item->quantity, 'cancel', 'pos_tab', $tab->tab_id, 'Tab cancelled');
                }
            }

            $tab->update([
                'status' => 'cancelled',
                'closed_by' => auth()->id(),
                'closed_at' => now(),
            ]);

            return $tab->fresh(['items.product']);
        });
    }

    public function reopenTab(PosTab $tab): PosTab
    {
        if ($tab->status !== 'closed' || $tab->order()->exists()) {
            throw new RuntimeException('Only unpaid closed tabs without orders can be reopened.');
        }

        $tab->update([
            'status' => 'open',
            'payment_method' => null,
            'closed_by' => null,
            'closed_at' => null,
        ]);

        return $tab->fresh(['items.product.category']);
    }

    public function transferTabBillingTarget(PosTab $tab, string $newTabType, ?int $folioId = null, ?int $creditAccountId = null, ?int $bookingId = null, ?int $roomId = null, ?int $guestId = null): PosTab
    {
        if ($tab->status !== 'open') {
            throw new RuntimeException('Can only transfer open tabs.');
        }

        if (! in_array($newTabType, ['walk_in', 'room', 'account'])) {
            throw new \InvalidArgumentException('Invalid tab type.');
        }

        if ($newTabType === 'room' && ! $folioId) {
            throw new \InvalidArgumentException('Folio ID is required for room charge.');
        }

        if ($newTabType === 'account' && ! $creditAccountId) {
            throw new \InvalidArgumentException('Credit Account ID is required for account charge.');
        }

        $oldType = $tab->tab_type;

        $tab->update([
            'tab_type' => $newTabType,
            'folio_id' => $newTabType === 'room' ? $folioId : null,
            'credit_account_id' => $newTabType === 'account' ? $creditAccountId : null,
            'booking_id' => $newTabType === 'room' ? $bookingId : null,
            'room_id' => $newTabType === 'room' ? $roomId : null,
            'guest_id' => $newTabType === 'room' ? $guestId : null,
        ]);

        ActivityLog::log(
            'POS_TAB_TRANSFER',
            "Transferred Tab #{$tab->tab_id} billing target from {$oldType} to {$newTabType}"
        );

        return $tab->fresh(['room', 'guest', 'folio', 'creditAccount']);
    }

    public function applyDiscount(PosTab $tab, string $type, float $amount, bool $isPercentage): PosTab
    {
        if ($tab->status !== 'open') {
            throw new RuntimeException('Cannot apply discount to closed tab.');
        }

        $tab->update([
            'discount_type' => $type,
            'discount_amount' => $amount,
            'is_discount_percentage' => $isPercentage,
        ]);

        $tab->recalculateTotals();

        return $tab->fresh();
    }

    public function removeDiscount(PosTab $tab): PosTab
    {
        if ($tab->status !== 'open') {
            throw new RuntimeException('Cannot remove discount from closed tab.');
        }

        $tab->update([
            'discount_type' => null,
            'discount_amount' => 0,
            'is_discount_percentage' => false,
        ]);

        $tab->recalculateTotals();

        return $tab->fresh();
    }

    public function formatTab(PosTab $tab): array
    {
        $tab->loadMissing(['items.product.category', 'room', 'guest', 'folio', 'creditAccount']);

        return [
            'tab_id' => $tab->tab_id,
            'tab_name' => $tab->tab_name,
            'tab_type' => $tab->tab_type,
            'status' => $tab->status,
            'guest_id' => $tab->guest_id,
            'folio_id' => $tab->folio_id,
            'credit_account_id' => $tab->credit_account_id,
            'credit_account_name' => $tab->creditAccount?->account_name,
            'booking_id' => $tab->booking_id,
            'room_id' => $tab->room_id,
            'room_number' => $tab->room?->room_number,
            'discount_type' => $tab->discount_type,
            'discount_amount' => (float) $tab->discount_amount,
            'is_discount_percentage' => (bool) $tab->is_discount_percentage,
            'subtotal' => (float) $tab->subtotal,
            'total' => (float) $tab->total,
            'item_count' => $tab->items->sum('quantity'),
            'pending_cancel_request' => $tab->approvalRequests()->where('status', 'pending')->exists(),
            'items' => $tab->items->map(fn (PosTabItem $item) => [
                'tab_item_id' => $item->tab_item_id,
                'product_id' => $item->product_id,
                'name' => $item->product?->name,
                'description' => $item->product?->description,
                'category' => $item->product?->category?->name,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'line_total' => (float) $item->line_total,
            ])->values()->all(),
        ];
    }
}
