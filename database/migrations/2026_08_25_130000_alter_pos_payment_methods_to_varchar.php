<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE pos_orders MODIFY payment_method VARCHAR(50) NULL');
            DB::statement('ALTER TABLE pos_tabs MODIFY payment_method VARCHAR(50) NULL');
            DB::statement("ALTER TABLE transactions MODIFY payment_method VARCHAR(50) NOT NULL DEFAULT 'NONE'");
            DB::statement("ALTER TABLE folios MODIFY payment_method VARCHAR(50) NULL DEFAULT 'Cash'");
        } else {
            Schema::table('pos_orders', function (Blueprint $table) {
                $table->string('payment_method', 50)->nullable()->change();
            });

            Schema::table('pos_tabs', function (Blueprint $table) {
                $table->string('payment_method', 50)->nullable()->change();
            });

            Schema::table('transactions', function (Blueprint $table) {
                $table->string('payment_method', 50)->default('NONE')->change();
            });

            Schema::table('folios', function (Blueprint $table) {
                $table->string('payment_method', 50)->nullable()->default('Cash')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Leaving column types as varchar to prevent data truncation during rollback
    }
};
