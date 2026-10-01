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
        Schema::create('facility_reservation_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('prefix', 30);
            $table->date('reservation_date');
            $table->unsignedInteger('last_sequence')->default(0);
            $table->timestamps();

            $table->unique(['prefix', 'reservation_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_reservation_sequences');
    }
};
