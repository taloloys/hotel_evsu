<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add ACCOUNT_CHARGE to the payment_method enum
        DB::statement("ALTER TABLE transactions MODIFY payment_method ENUM('CASH', 'CREDIT_CARD', 'CHECK', 'NONE', 'ACCOUNT_CHARGE') DEFAULT 'NONE'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE transactions MODIFY payment_method ENUM('CASH', 'CREDIT_CARD', 'CHECK', 'NONE') DEFAULT 'NONE'");
    }
};
