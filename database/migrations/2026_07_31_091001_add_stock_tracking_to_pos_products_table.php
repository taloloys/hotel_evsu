<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Replace the boolean `is_stockable` column with a
     * `stock_tracking` string column: 'manual' or 'none'.
     *
     * - manual  → physical countable items (Beer, Coffee Beans, Fresh Milk…)
     * - none    → made-to-order items (Americano, Cappuccino, Club Sandwich…)
     */
    public function up(): void
    {
        Schema::table('pos_products', function (Blueprint $table) {
            $table->string('stock_tracking', 20)->default('manual')->after('is_stockable');
        });

        // Migrate existing data: is_stockable = true → 'manual', false → 'none'
        DB::statement("UPDATE pos_products SET stock_tracking = CASE WHEN is_stockable = 1 THEN 'manual' ELSE 'none' END");

        Schema::table('pos_products', function (Blueprint $table) {
            $table->dropColumn('is_stockable');
        });
    }

    public function down(): void
    {
        Schema::table('pos_products', function (Blueprint $table) {
            $table->boolean('is_stockable')->default(true)->after('price');
        });

        DB::statement("UPDATE pos_products SET is_stockable = CASE WHEN stock_tracking = 'manual' THEN 1 ELSE 0 END");

        Schema::table('pos_products', function (Blueprint $table) {
            $table->dropColumn('stock_tracking');
        });
    }
};
