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
            DB::statement("ALTER TABLE transactions MODIFY payment_method VARCHAR(50) NOT NULL DEFAULT 'NONE'");
            DB::statement("ALTER TABLE folios MODIFY payment_method VARCHAR(50) NULL DEFAULT 'Cash'");
        } else {
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
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE transactions MODIFY payment_method ENUM('CASH', 'CREDIT_CARD', 'CHECK', 'NONE', 'ACCOUNT_CHARGE', 'GCASH', 'MAYA') DEFAULT 'NONE'");
            DB::statement("ALTER TABLE folios MODIFY payment_method ENUM('Cash', 'Credit Card', 'GCash', 'Maya') DEFAULT 'Cash'");
        } else {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('payment_method', 30)->default('NONE')->change();
            });

            Schema::table('folios', function (Blueprint $table) {
                $table->string('payment_method', 30)->nullable()->default('Cash')->change();
            });
        }
    }
};
