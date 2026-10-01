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
        Schema::create('facility_set_facility', function (Blueprint $table) {
            $table->foreignId('facility_set_id')->constrained('facility_sets', 'facility_set_id')->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained('facilities', 'facility_id')->cascadeOnDelete();
            $table->primary(['facility_set_id', 'facility_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_set_facility');
    }
};
