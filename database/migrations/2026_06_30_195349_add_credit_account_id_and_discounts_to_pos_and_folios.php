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
        Schema::table('folios', function (Blueprint $table) {
            $table->unsignedInteger('credit_account_id')->nullable()->after('guest_id');
            $table->foreign('credit_account_id')->references('account_id')->on('credit_accounts')->nullOnDelete();
        });

        Schema::table('pos_tabs', function (Blueprint $table) {
            $table->unsignedInteger('credit_account_id')->nullable()->after('folio_id');
            $table->string('discount_type', 50)->nullable()->after('status');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_type');
            $table->boolean('is_discount_percentage')->default(false)->after('discount_amount');
            $table->foreign('credit_account_id')->references('account_id')->on('credit_accounts')->nullOnDelete();
        });

        Schema::table('pos_orders', function (Blueprint $table) {
            $table->unsignedInteger('credit_account_id')->nullable()->after('folio_id');
            $table->string('discount_type', 50)->nullable()->after('status');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_type');
            $table->boolean('is_discount_percentage')->default(false)->after('discount_amount');
            $table->foreign('credit_account_id')->references('account_id')->on('credit_accounts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_orders', function (Blueprint $table) {
            $table->dropForeign(['credit_account_id']);
            $table->dropColumn(['credit_account_id', 'discount_type', 'discount_amount', 'is_discount_percentage']);
        });

        Schema::table('pos_tabs', function (Blueprint $table) {
            $table->dropForeign(['credit_account_id']);
            $table->dropColumn(['credit_account_id', 'discount_type', 'discount_amount', 'is_discount_percentage']);
        });

        Schema::table('folios', function (Blueprint $table) {
            $table->dropForeign(['credit_account_id']);
            $table->dropColumn('credit_account_id');
        });
    }
};
