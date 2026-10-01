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
        Schema::create('facility_sets', function (Blueprint $table) {
            $table->id('facility_set_id');
            $table->string('name');
            $table->string('prefix_code', 30)->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->decimal('daily_rate', 10, 2)->nullable();
            $table->decimal('rate', 10, 2)->default(0);
            $table->enum('rate_type', ['hourly', 'daily'])->default('hourly');
            $table->json('images')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_sets');
    }
};
