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
        Schema::create('shift_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->string('shift_name', 100);
            $table->date('shift_date');
            $table->time('scheduled_start_time');
            $table->time('scheduled_end_time');
            $table->text('notes')->nullable();
            $table->enum('status', ['SCHEDULED', 'ACTIVE', 'COMPLETED', 'MISSED'])->default('SCHEDULED');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_schedules');
    }
};
