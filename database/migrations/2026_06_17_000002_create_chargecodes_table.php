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
        Schema::create('chargecodes', function (Blueprint $table) {
            $table->increments('charge_code');
            $table->string('description', 100);
            $table->enum('category', ['HOTEL', 'RESTAURANT', 'TAX_SERVICE', 'PAYMENT']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chargecodes');
    }
};
