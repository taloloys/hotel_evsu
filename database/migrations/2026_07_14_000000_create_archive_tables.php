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
        Schema::create('archived_transactions', function (Blueprint $table) {
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
            $table->string('department', 50)->nullable();
            $table->timestamp('timestamp')->useCurrent();
            $table->timestamp('archived_at')->nullable();

            $table->foreign('folio_id')->references('folio_id')->on('folios');
            $table->foreign('charge_code')->references('charge_code')->on('chargecodes');
            $table->foreign('shift_id')->references('shift_id')->on('shifts');
            $table->foreign('user_id')->references('user_id')->on('users');
        });

        Schema::create('archived_expenses', function (Blueprint $table) {
            $table->increments('expense_id');
            $table->date('expense_date');
            $table->string('department', 100);
            $table->string('purpose', 255)->nullable();
            $table->string('category', 100);
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('APPROVED');
            $table->decimal('amount', 10, 2);
            $table->unsignedInteger('user_id')->index();
            $table->string('funding_source', 100)->nullable();
            $table->string('requested_by', 100)->nullable();
            $table->timestamps();
            $table->timestamp('archived_at')->nullable();

            $table->foreign('user_id')->references('user_id')->on('users');
        });

        Schema::create('archived_activitylogs', function (Blueprint $table) {
            $table->increments('log_id');
            $table->unsignedInteger('user_id')->index();
            $table->enum('action_type', ['LOGIN', 'RESERVATION_CREATE', 'CHECK_IN', 'ADD_CHARGE', 'PRINT_FOLIO', 'CLOSE_SHIFT', 'ROOM_MODIFIED']);
            $table->text('description');
            $table->timestamp('timestamp')->useCurrent();
            $table->timestamp('archived_at')->nullable();

            $table->foreign('user_id')->references('user_id')->on('users');
        });

        Schema::create('archived_pos_orders', function (Blueprint $table) {
            $table->increments('order_id');
            $table->string('order_number', 30)->unique();
            $table->unsignedInteger('tab_id')->nullable()->index();
            $table->unsignedInteger('folio_id')->nullable()->index();
            $table->unsignedInteger('credit_account_id')->nullable()->index();
            $table->unsignedInteger('transaction_id')->nullable()->index();
            $table->string('customer_name', 150);
            $table->string('room_number', 20)->nullable();
            $table->enum('status', ['open', 'active', 'closed', 'cancelled', 'refunded'])->default('closed')->index();
            $table->string('discount_type', 50)->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->boolean('is_discount_percentage')->default(false);
            $table->enum('payment_method', ['cash', 'room_charge', 'credit_account'])->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('shift_id')->index();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            $table->foreign('tab_id')->references('tab_id')->on('pos_tabs')->nullOnDelete();
            $table->foreign('folio_id')->references('folio_id')->on('folios');
            $table->foreign('transaction_id')->references('transaction_id')->on('transactions');
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('shift_id')->references('shift_id')->on('shifts');
        });

        Schema::create('archived_pos_order_items', function (Blueprint $table) {
            $table->increments('order_item_id');
            $table->unsignedInteger('order_id')->index();
            $table->unsignedInteger('product_id')->index();
            $table->string('product_name', 150);
            $table->string('product_description', 255)->nullable();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('line_total', 10, 2);
            $table->timestamp('archived_at')->nullable();

            $table->foreign('order_id')->references('order_id')->on('pos_orders')->cascadeOnDelete();
            $table->foreign('product_id')->references('product_id')->on('pos_products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archived_pos_order_items');
        Schema::dropIfExists('archived_pos_orders');
        Schema::dropIfExists('archived_activitylogs');
        Schema::dropIfExists('archived_expenses');
        Schema::dropIfExists('archived_transactions');
    }
};
