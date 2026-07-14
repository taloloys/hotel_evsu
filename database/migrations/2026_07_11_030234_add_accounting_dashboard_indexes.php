<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            Schema::table('expenses', function (Blueprint $table) {
                $table->index('expense_date', 'idx_expenses_expense_date');
            });
        } catch (QueryException $e) {
            // Ignore if index already exists
        }

        try {
            Schema::table('transactions', function (Blueprint $table) {
                $table->index('payment_method', 'idx_transactions_payment_method');
            });
        } catch (QueryException $e) {
            // Ignore if index already exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('idx_expenses_expense_date');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_transactions_payment_method');
        });
    }
};
