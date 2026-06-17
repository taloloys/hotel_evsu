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
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('booking_id');
            $table->unsignedInteger('folio_id')->index();
            $table->unsignedInteger('room_id')->index();
            $table->date('arrival_date');
            $table->time('arrival_time')->nullable();
            $table->date('departure_date');
            $table->time('departure_time')->nullable();
            $table->dateTime('actual_check_in')->nullable();
            $table->dateTime('actual_check_out')->nullable();
            $table->enum('status', ['RESERVED', 'CHECKED_IN', 'CHECKED_OUT', 'CANCELLED'])->default('RESERVED');

            $table->foreign('folio_id')->references('folio_id')->on('folios');
            $table->foreign('room_id')->references('room_id')->on('rooms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
