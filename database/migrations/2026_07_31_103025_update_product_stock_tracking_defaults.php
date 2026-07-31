<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update stock_tracking for existing made-to-order products vs manual physical goods.
     */
    public function up(): void
    {
        $madeToOrder = [
            'Americano',
            'Latte',
            'Cappuccino',
            'Green Tea',
            'Club Sandwich',
            'French Fries',
            'Cookies',
            'Donut',
            'Melon Juice',
        ];

        DB::table('pos_products')
            ->whereIn('name', $madeToOrder)
            ->update([
                'stock_tracking' => 'none',
                'stock_quantity' => 0,
                'low_stock_threshold' => null,
            ]);

        $manualItems = [
            'Beer',
            'Red Horse',
            'Coffee Beans',
            'Fresh Milk',
            'San Miguel Apple',
            'Soju',
        ];

        DB::table('pos_products')
            ->whereIn('name', $manualItems)
            ->update([
                'stock_tracking' => 'manual',
            ]);
    }

    public function down(): void
    {
        // No revert required for data backfill migration
    }
};
