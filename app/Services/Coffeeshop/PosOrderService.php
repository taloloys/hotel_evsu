<?php

namespace App\Services\Coffeeshop;

use App\Models\ActivityLog;
use App\Models\PosInventoryLog;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\PosProduct;
use App\Models\PosTab;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PosOrderService
{
    public function __construct(
        private PosInventoryService $inventoryService,
        private PosGuestChargeService $chargeService,
        private PosTabService $tabService,
    ) {}

    public function closeTab(PosTab $tab, string $paymentMethod, ?int $bookingId = null, ?int $folioId = null): PosOrder
    {
        if ($tab->status !== 'open') {
            throw new RuntimeException('Tab is not open.');
        }

        $tab->load('items.product');

        if ($tab->items->isEmpty()) {
            throw new RuntimeException('Tab has no items.');
        }

        return DB::transaction(function () use ($tab, $paymentMethod, $bookingId, $folioId) {
            $userId = auth()->id() ?? 1;
            $shift = $this->chargeService->resolveActiveShift($userId);
            $orderNumber = $this->generateOrderNumber();
            $roomNumber = $tab->room?->room_number;
            $folioIdToUse = $folioId ?? $tab->folio_id;

            if ($paymentMethod === 'room_charge') {
                $booking = $this->chargeService->validateRoomCharge($bookingId ?? $tab->booking_id, $folioIdToUse);
                $folioIdToUse = $booking->folio_id;
                $roomNumber = $booking->room?->room_number ?? $roomNumber;
            }

            $order = PosOrder::create([
                'order_number' => $orderNumber,
                'tab_id' => $tab->tab_id,
                'folio_id' => $paymentMethod === 'room_charge' ? $folioIdToUse : null,
                'customer_name' => $tab->tab_name,
                'room_number' => $roomNumber,
                'status' => 'closed',
                'payment_method' => $paymentMethod,
                'subtotal' => $tab->subtotal,
                'total' => $tab->total,
                'user_id' => $userId,
                'shift_id' => $shift->shift_id,
                'closed_at' => now(),
            ]);

            $itemSummaryParts = [];

            foreach ($tab->items as $item) {
                $product = $item->product ?? PosProduct::findOrFail($item->product_id);

                PosOrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $product->product_id,
                    'product_name' => $product->name,
                    'product_description' => $product->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->line_total,
                ]);

                // Since stock was dynamically deducted during tab edits, we update the log reference from tab to order
                PosInventoryLog::where('reference_type', 'pos_tab')
                    ->where('reference_id', $tab->tab_id)
                    ->where('product_id', $product->product_id)
                    ->update([
                        'reference_type' => 'pos_order',
                        'reference_id' => $order->order_id,
                    ]);
                $itemSummaryParts[] = "{$product->name} x{$item->quantity}";
            }

            $itemSummary = implode(', ', $itemSummaryParts);
            if (strlen($itemSummary) > 200) {
                $itemSummary = substr($itemSummary, 0, 197).'...';
            }

            if ($paymentMethod === 'room_charge') {
                $transaction = $this->chargeService->postRoomCharge($order, (int) $folioIdToUse, $itemSummary);
                $order->update(['transaction_id' => $transaction->transaction_id, 'folio_id' => $folioIdToUse]);
            } else {
                $this->chargeService->postWalkInSale($order, $paymentMethod, $itemSummary);
            }

            $tab->update([
                'status' => 'closed',
                'payment_method' => $paymentMethod,
                'folio_id' => $paymentMethod === 'room_charge' ? $folioIdToUse : $tab->folio_id,
                'closed_by' => $userId,
                'closed_at' => now(),
            ]);

            ActivityLog::log(
                'ADD_CHARGE',
                "POS order {$order->order_number} closed ({$paymentMethod}) for {$tab->tab_name}, total ₱".number_format((float) $order->total, 2).'.'
            );

            return $order->fresh(['items', 'folio', 'transaction']);
        });
    }

    public function refundOrder(PosOrder $order): PosOrder
    {
        if ($order->status !== 'closed') {
            throw new RuntimeException('Only closed orders can be refunded.');
        }

        return DB::transaction(function () use ($order) {
            $order->load('items.product');

            foreach ($order->items as $item) {
                $product = $item->product ?? PosProduct::find($item->product_id);
                if ($product) {
                    $this->inventoryService->restoreForRefund($product, $item->quantity, $order->order_id);
                }
            }

            $order->update(['status' => 'refunded']);

            ActivityLog::log(
                'ADD_CHARGE',
                "POS order {$order->order_number} refunded."
            );

            return $order->fresh(['items']);
        });
    }

    public function cancelOrder(PosOrder $order): PosOrder
    {
        if (! in_array($order->status, ['open', 'active'], true)) {
            throw new RuntimeException('Only open or active orders can be cancelled.');
        }

        $order->update(['status' => 'cancelled']);

        return $order->fresh(['items']);
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'POS-'.now()->format('Ymd');
        $latest = PosOrder::where('order_number', 'like', $prefix.'%')
            ->orderByDesc('order_id')
            ->value('order_number');

        $sequence = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return sprintf('%s-%04d', $prefix, $sequence);
    }
}
