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
        Schema::create('activitylogs', function (Blueprint $table) {
            $table->increments('log_id');
            $table->unsignedInteger('user_id')->index();
            $table->enum('action_type', ['LOGIN', 'RESERVATION_CREATE', 'CHECK_IN', 'ADD_CHARGE', 'PRINT_FOLIO', 'CLOSE_SHIFT', 'ROOM_MODIFIED']);
            $table->text('description');
            $table->timestamp('timestamp')->useCurrent();

            $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activitylogs');
    }
};
