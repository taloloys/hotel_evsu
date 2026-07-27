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
        Schema::create('landing_page_showcases', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['ROOM', 'CAFETERIA_MAIN', 'CAFETERIA_ITEM'])->default('ROOM');
            $table->string('title');
            $table->string('category')->nullable();
            $table->string('price_rate')->nullable();
            $table->string('capacity')->nullable();
            $table->string('badge')->nullable();
            $table->string('timing')->nullable();
            $table->string('icon')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_showcases');
    }
};
