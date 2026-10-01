<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->decimal('hourly_rate', 10, 2)->nullable()->after('capacity');
            $table->decimal('daily_rate', 10, 2)->nullable()->after('hourly_rate');
        });

        // Backfill existing facilities
        DB::table('facilities')->where('rate_type', 'hourly')->update([
            'hourly_rate' => DB::raw('rate'),
        ]);

        DB::table('facilities')->where('rate_type', 'daily')->update([
            'daily_rate' => DB::raw('rate'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn(['hourly_rate', 'daily_rate']);
        });
    }
};
