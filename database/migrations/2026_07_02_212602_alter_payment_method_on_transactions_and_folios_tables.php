<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_method', 30)->default('NONE')->change();
        });

        Schema::table('folios', function (Blueprint $table) {
            $table->string('payment_method', 30)->default('Cash')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('payment_method', ['CASH', 'CREDIT_CARD', 'CHECK', 'NONE'])->default('NONE')->change();
        });

        Schema::table('folios', function (Blueprint $table) {
            $table->enum('payment_method', ['Cash', 'Credit Card'])->default('Cash')->change();
        });
    }
};
