<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `rooms` MODIFY `status` ENUM('AVAILABLE', 'OCCUPIED', 'RESERVED', 'CLEANING', 'MAINTENANCE') NOT NULL DEFAULT 'AVAILABLE'");

        DB::table('rooms')->where('status', 'MAINTENANCE')->update(['status' => 'CLEANING']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('rooms')->where('status', 'CLEANING')->update(['status' => 'MAINTENANCE']);

        DB::statement("ALTER TABLE `rooms` MODIFY `status` ENUM('AVAILABLE', 'OCCUPIED', 'RESERVED', 'MAINTENANCE') NOT NULL DEFAULT 'AVAILABLE'");
    }
};
