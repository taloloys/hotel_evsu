<?php

namespace App\Services\Coffeeshop;

use App\Models\PosProduct;
use App\Models\PosTab;
use App\Models\PosTabItem;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PosTabService
{
    public function openTab(array $data): PosTab
    {
        return PosTab::create([
            'tab_name' => $data['tab_name'],
            'tab_type' => $data['tab_type'] ?? 'walk_in',
            'guest_id' => $data['guest_id'] ?? null,
            'folio_id' => $data['folio_id'] ?? null,
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
            $existing = PosTabItem::where('tab_id', $tab->tab_id)
                ->where('product_id', $product->product_id)
                ->first();

            if ($existing) {
                $newQty = $existing->quantity + $quantity;

                if ($product->stock_quantity < $newQty) {
                    throw new RuntimeException("Insufficient stock for {$product->name}. Available: {$product->stock_quantity}.");
                }

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

        if ($product->stock_quantity < $quantity) {
            throw new RuntimeException("Insufficient stock for {$product->name}. Available: {$product->stock_quantity}.");
        }

        $item->update([
            'quantity' => $quantity,
            'line_total' => $quantity * $item->unit_price,
        ]);

        $tab->recalculateTotals();

        return $tab->fresh(['items.product.category', 'room', 'guest', 'folio']);
    }

    public function removeItem(PosTab $tab, PosTabItem $item): PosTab
    {
        if ($tab->status !== 'open') {
            throw new RuntimeException('Cannot modify a closed or cancelled tab.');
        }

        $item->delete();
        $tab->recalculateTotals();

        return $tab->fresh(['items.product.category', 'room', 'guest', 'folio']);
    }

    public function cancelTab(PosTab $tab): PosTab
    {
        if ($tab->status !== 'open') {
            throw new RuntimeException('Only open tabs can be cancelled.');
        }

        $tab->update([
            'status' => 'cancelled',
            'closed_by' => auth()->id(),
            'closed_at' => now(),
        ]);

        return $tab->fresh(['items.product']);
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

    public function formatTab(PosTab $tab): array
    {
        $tab->loadMissing(['items.product.category', 'room', 'guest', 'folio']);

        return [
            'tab_id' => $tab->tab_id,
            'tab_name' => $tab->tab_name,
            'tab_type' => $tab->tab_type,
            'status' => $tab->status,
            'guest_id' => $tab->guest_id,
            'folio_id' => $tab->folio_id,
            'booking_id' => $tab->booking_id,
            'room_id' => $tab->room_id,
            'room_number' => $tab->room?->room_number,
            'subtotal' => (float) $tab->subtotal,
            'total' => (float) $tab->total,
            'item_count' => $tab->items->sum('quantity'),
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
