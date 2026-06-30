<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE activitylogs MODIFY COLUMN action_type ENUM('LOGIN', 'RESERVATION_CREATE', 'CHECK_IN', 'ADD_CHARGE', 'PRINT_FOLIO', 'CLOSE_SHIFT', 'ROOM_MODIFIED', 'SYSTEM_SETTING') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE activitylogs MODIFY COLUMN action_type ENUM('LOGIN', 'RESERVATION_CREATE', 'CHECK_IN', 'ADD_CHARGE', 'PRINT_FOLIO', 'CLOSE_SHIFT', 'ROOM_MODIFIED') NOT NULL");
    }
};
