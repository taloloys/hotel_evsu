<?php

namespace App\Services\Coffeeshop;

use App\Models\PosInventoryLog;
use App\Models\PosProduct;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PosInventoryService
{
    public function adjustStock(
        PosProduct $product,
        int $changeQty,
        string $reason,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null
    ): PosProduct {
        return DB::transaction(function () use ($product, $changeQty, $reason, $referenceType, $referenceId, $notes) {
            $locked = PosProduct::where('product_id', $product->product_id)->lockForUpdate()->firstOrFail();
            $newQty = $locked->stock_quantity + $changeQty;

            if ($newQty < 0) {
                throw new RuntimeException("Insufficient stock for {$locked->name}. Available: {$locked->stock_quantity}.");
            }

            $locked->update(['stock_quantity' => $newQty]);

            PosInventoryLog::create([
                'product_id' => $locked->product_id,
                'change_qty' => $changeQty,
                'reason' => $reason,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'user_id' => auth()->id() ?? 1,
                'notes' => $notes,
            ]);

            return $locked->fresh(['category']);
        });
    }

    public function decrementForSale(PosProduct $product, int $quantity, int $orderId): PosProduct
    {
        return $this->adjustStock(
            $product,
            -$quantity,
            'sale',
            'pos_order',
            $orderId,
            "Sold {$quantity} unit(s)"
        );
    }

    public function restoreForRefund(PosProduct $product, int $quantity, int $orderId): PosProduct
    {
        return $this->adjustStock(
            $product,
            $quantity,
            'refund',
            'pos_order',
            $orderId,
            "Refunded {$quantity} unit(s)"
        );
    }

    public function lowStockProducts()
    {
        return PosProduct::with('category')->lowStock()->orderBy('stock_quantity')->get();
    }

    public function lowStockCount(): int
    {
        return PosProduct::lowStock()->count();
    }
}
