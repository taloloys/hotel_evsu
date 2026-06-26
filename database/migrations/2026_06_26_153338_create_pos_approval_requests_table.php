<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_approval_requests', function (Blueprint $table) {
            $table->increments('request_id');
            $table->unsignedInteger('order_id')->nullable()->index();
            $table->unsignedInteger('tab_id')->nullable()->index();
            $table->enum('request_type', ['refund', 'cancel_tab', 'cancel_order'])->index();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->unsignedInteger('requested_by')->index();
            $table->unsignedInteger('resolved_by')->nullable()->index();
            $table->string('reason', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();

            $table->foreign('order_id')->references('order_id')->on('pos_orders')->nullOnDelete();
            $table->foreign('tab_id')->references('tab_id')->on('pos_tabs')->nullOnDelete();
            $table->foreign('requested_by')->references('user_id')->on('users');
            $table->foreign('resolved_by')->references('user_id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_approval_requests');
    }
};
