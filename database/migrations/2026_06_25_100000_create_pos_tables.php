<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_categories', function (Blueprint $table) {
            $table->increments('category_id');
            $table->string('name', 100)->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('pos_settings', function (Blueprint $table) {
            $table->string('setting_key', 100)->primary();
            $table->string('setting_value', 255);
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::create('pos_products', function (Blueprint $table) {
            $table->increments('product_id');
            $table->unsignedInteger('category_id')->index();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image_path', 255)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->unsignedInteger('low_stock_threshold')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('category_id')->references('category_id')->on('pos_categories');
            $table->index(['name', 'is_active']);
        });

        Schema::create('pos_tabs', function (Blueprint $table) {
            $table->increments('tab_id');
            $table->string('tab_name', 150);
            $table->enum('tab_type', ['walk_in', 'room'])->default('walk_in');
            $table->unsignedInteger('guest_id')->nullable()->index();
            $table->unsignedInteger('folio_id')->nullable()->index();
            $table->unsignedInteger('booking_id')->nullable()->index();
            $table->unsignedInteger('room_id')->nullable()->index();
            $table->enum('status', ['open', 'closed', 'cancelled'])->default('open')->index();
            $table->enum('payment_method', ['cash', 'room_charge'])->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->unsignedInteger('opened_by')->index();
            $table->unsignedInteger('closed_by')->nullable();
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();

            $table->foreign('guest_id')->references('guest_id')->on('guests');
            $table->foreign('folio_id')->references('folio_id')->on('folios');
            $table->foreign('booking_id')->references('booking_id')->on('bookings');
            $table->foreign('room_id')->references('room_id')->on('rooms');
            $table->foreign('opened_by')->references('user_id')->on('users');
            $table->foreign('closed_by')->references('user_id')->on('users');
        });

        Schema::create('pos_tab_items', function (Blueprint $table) {
            $table->increments('tab_item_id');
            $table->unsignedInteger('tab_id')->index();
            $table->unsignedInteger('product_id')->index();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('line_total', 10, 2);

            $table->foreign('tab_id')->references('tab_id')->on('pos_tabs')->cascadeOnDelete();
            $table->foreign('product_id')->references('product_id')->on('pos_products');
        });

        Schema::create('pos_orders', function (Blueprint $table) {
            $table->increments('order_id');
            $table->string('order_number', 30)->unique();
            $table->unsignedInteger('tab_id')->nullable()->index();
            $table->unsignedInteger('folio_id')->nullable()->index();
            $table->unsignedInteger('transaction_id')->nullable()->index();
            $table->string('customer_name', 150);
            $table->string('room_number', 20)->nullable();
            $table->enum('status', ['open', 'active', 'closed', 'cancelled', 'refunded'])->default('closed')->index();
            $table->enum('payment_method', ['cash', 'room_charge'])->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('shift_id')->index();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();

            $table->foreign('tab_id')->references('tab_id')->on('pos_tabs')->nullOnDelete();
            $table->foreign('folio_id')->references('folio_id')->on('folios');
            $table->foreign('transaction_id')->references('transaction_id')->on('transactions');
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('shift_id')->references('shift_id')->on('shifts');
        });

        Schema::create('pos_order_items', function (Blueprint $table) {
            $table->increments('order_item_id');
            $table->unsignedInteger('order_id')->index();
            $table->unsignedInteger('product_id')->index();
            $table->string('product_name', 150);
            $table->string('product_description', 255)->nullable();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('line_total', 10, 2);

            $table->foreign('order_id')->references('order_id')->on('pos_orders')->cascadeOnDelete();
            $table->foreign('product_id')->references('product_id')->on('pos_products');
        });

        Schema::create('pos_inventory_logs', function (Blueprint $table) {
            $table->increments('log_id');
            $table->unsignedInteger('product_id')->index();
            $table->integer('change_qty');
            $table->enum('reason', ['sale', 'restock', 'adjustment', 'refund', 'cancel']);
            $table->string('reference_type', 50)->nullable();
            $table->unsignedInteger('reference_id')->nullable();
            $table->unsignedInteger('user_id')->index();
            $table->string('notes', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('product_id')->references('product_id')->on('pos_products');
            $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_inventory_logs');
        Schema::dropIfExists('pos_order_items');
        Schema::dropIfExists('pos_orders');
        Schema::dropIfExists('pos_tab_items');
        Schema::dropIfExists('pos_tabs');
        Schema::dropIfExists('pos_products');
        Schema::dropIfExists('pos_settings');
        Schema::dropIfExists('pos_categories');
    }
};
