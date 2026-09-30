<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Expand room_number from varchar(10) to varchar(50) to accommodate
     * facility names (e.g. "Function Hall A", "Conference Room 1").
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropUnique('rooms_room_number_unique');
            $table->string('room_number', 50)->change();
            $table->unique('room_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropUnique('rooms_room_number_unique');
            $table->string('room_number', 10)->change();
            $table->unique('room_number');
        });
    }
};
