<?php

namespace App\Services\Coffeeshop;

use App\Models\ActivityLog;
use App\Models\CreditAccount;
use App\Models\PosInventoryLog;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\PosProduct;
use App\Models\PosTab;
use App\Services\CreditBillingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PosOrderService
{
    public function __construct(
        private PosInventoryService $inventoryService,
        private PosGuestChargeService $chargeService,
        private PosTabService $tabService,
        private CreditBillingService $creditService,
    ) {}

    public function closeTab(PosTab $tab, string $paymentMethod, ?int $bookingId = null, ?int $folioId = null, ?int $creditAccountId = null): PosOrder
    {
        if ($tab->status !== 'open') {
            throw new RuntimeException('Tab is not open.');
        }

        $tab->load('items.product');

        if ($tab->items->isEmpty()) {
            throw new RuntimeException('Tab has no items.');
        }

        return DB::transaction(function () use ($tab, $paymentMethod, $bookingId, $folioId, $creditAccountId) {
            $userId = auth()->id() ?? 1;
            $shift = $this->chargeService->resolveActiveShift($userId);
            $orderNumber = $this->generateOrderNumber();
            $roomNumber = $tab->room?->room_number;
            $folioIdToUse = $folioId ?? $tab->folio_id;
            $creditAccountIdToUse = $creditAccountId ?? $tab->credit_account_id;

            if ($paymentMethod === 'room_charge') {
                $booking = $this->chargeService->validateRoomCharge($bookingId ?? $tab->booking_id, $folioIdToUse);
                $folioIdToUse = $booking->folio_id;
                $roomNumber = $booking->room?->room_number ?? $roomNumber;
            }

            $order = PosOrder::create([
                'order_number' => $orderNumber,
                'tab_id' => $tab->tab_id,
                'folio_id' => $paymentMethod === 'room_charge' ? $folioIdToUse : null,
                'credit_account_id' => $paymentMethod === 'account_charge' ? $creditAccountIdToUse : null,
                'customer_name' => $tab->tab_name,
                'room_number' => $roomNumber,
                'status' => 'closed',
                'payment_method' => $paymentMethod,
                'discount_type' => $tab->discount_type,
                'discount_amount' => $tab->discount_amount,
                'is_discount_percentage' => $tab->is_discount_percentage,
                'subtotal' => $tab->subtotal,
                'total' => $tab->total,
                'user_id' => $userId,
                'shift_id' => $shift->shift_id,
                'closed_at' => now(),
            ]);

            $itemSummaryParts = [];
            $receiptContent = "RECEIPT\nOrder: {$orderNumber}\nDate: ".now()->format('Y-m-d H:i:s')."\n";
            $receiptContent .= "Customer: {$tab->tab_name}\n-----------------------------------\n";

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

                PosInventoryLog::where('reference_type', 'pos_tab')
                    ->where('reference_id', $tab->tab_id)
                    ->where('product_id', $product->product_id)
                    ->update([
                        'reference_type' => 'pos_order',
                        'reference_id' => $order->order_id,
                    ]);
                $itemSummaryParts[] = "{$product->name} x{$item->quantity}";

                $receiptContent .= "{$product->name} x{$item->quantity} @ ₱{$item->unit_price} = ₱{$item->line_total}\n";
            }

            $receiptContent .= "-----------------------------------\n";
            $receiptContent .= "Subtotal: ₱{$tab->subtotal}\n";
            if ($tab->discount_amount > 0) {
                $discountStr = $tab->is_discount_percentage ? "{$tab->discount_amount}%" : "₱{$tab->discount_amount}";
                $receiptContent .= "Discount ({$tab->discount_type}): -{$discountStr}\n";
            }
            $receiptContent .= "Total: ₱{$tab->total}\n";
            $receiptContent .= "Payment Method: {$paymentMethod}\n";

            // Save receipt locally
            Storage::disk('local')->put("receipts/{$orderNumber}.txt", $receiptContent);

            $itemSummary = implode(', ', $itemSummaryParts);
            if (strlen($itemSummary) > 200) {
                $itemSummary = substr($itemSummary, 0, 197).'...';
            }

            if ($paymentMethod === 'room_charge') {
                $transaction = $this->chargeService->postRoomCharge($order, (int) $folioIdToUse, $itemSummary);
                $order->update(['transaction_id' => $transaction->transaction_id, 'folio_id' => $folioIdToUse]);
            } elseif ($paymentMethod === 'account_charge') {
                $account = CreditAccount::findOrFail($creditAccountIdToUse);
                $this->creditService->chargeAccount($account, $order->total, 'pos_order', $order->order_id, $userId, "POS Order {$orderNumber}: {$itemSummary}");
                $order->update(['credit_account_id' => $creditAccountIdToUse]);
            } else {
                $this->chargeService->postWalkInSale($order, $paymentMethod, $itemSummary);
            }

            $tab->update([
                'status' => 'closed',
                'payment_method' => $paymentMethod,
                'folio_id' => $paymentMethod === 'room_charge' ? $folioIdToUse : $tab->folio_id,
                'credit_account_id' => $paymentMethod === 'account_charge' ? $creditAccountIdToUse : $tab->credit_account_id,
                'closed_by' => $userId,
                'closed_at' => now(),
            ]);

            ActivityLog::log(
                'ADD_CHARGE',
                "POS order {$order->order_number} closed ({$paymentMethod}) for {$tab->tab_name}, total ₱".number_format((float) $order->total, 2).'.'
            );

            return $order->fresh(['items', 'folio', 'transaction', 'creditAccount']);
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
