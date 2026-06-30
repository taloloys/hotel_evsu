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
        Schema::create('credit_account_ledgers', function (Blueprint $table) {
            $table->increments('ledger_id');
            $table->unsignedInteger('account_id')->index();
            $table->enum('type', ['charge', 'payment'])->index(); // 'charge' increases balance (debt), 'payment' decreases balance
            $table->decimal('amount', 12, 2);
            $table->string('reference_type', 50)->nullable(); // e.g. 'pos_order', 'folio', 'manual'
            $table->unsignedInteger('reference_id')->nullable();
            $table->unsignedInteger('processed_by')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('account_id')->references('account_id')->on('credit_accounts')->cascadeOnDelete();
            $table->foreign('processed_by')->references('user_id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_account_ledgers');
    }
};
