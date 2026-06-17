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
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('transaction_id');
            $table->unsignedInteger('folio_id')->index();
            $table->unsignedInteger('charge_code')->index();
            $table->unsignedInteger('shift_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->date('transaction_date');
            $table->string('charge_number', 30)->nullable();
            $table->enum('payment_method', ['CASH', 'CREDIT_CARD', 'CHECK', 'NONE'])->default('NONE');
            $table->string('reference_notes', 255)->nullable();
            $table->decimal('charge_amount', 10, 2)->default(0.00);
            $table->decimal('credit_amount', 10, 2)->default(0.00);
            $table->timestamp('timestamp')->useCurrent();

            $table->foreign('folio_id')->references('folio_id')->on('folios');
            $table->foreign('charge_code')->references('charge_code')->on('chargecodes');
            $table->foreign('shift_id')->references('shift_id')->on('shifts');
            $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
