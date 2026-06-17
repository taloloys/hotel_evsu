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
        Schema::create('folios', function (Blueprint $table) {
            $table->increments('folio_id');
            $table->string('folio_number', 20)->unique();
            $table->string('registration_number', 20)->nullable()->unique();
            $table->string('account_number', 20)->nullable();
            $table->unsignedInteger('guest_id')->index();
            $table->string('market_segment', 50)->default('NONE');
            $table->text('billing_arrangements')->nullable();
            $table->text('special_arrangements')->nullable();
            $table->integer('num_pax')->default(1);
            $table->boolean('has_joiner')->default(false);
            $table->integer('num_free_breakfasts')->default(0);
            $table->string('breakfast_code', 20)->nullable();
            $table->string('symbol', 10)->default('CBO');
            $table->enum('folio_type', ['GUEST', 'HOUSE', 'ALL'])->default('GUEST');
            $table->enum('status', ['OPEN', 'CLOSED'])->default('OPEN');

            $table->foreign('guest_id')->references('guest_id')->on('guests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folios');
    }
};
