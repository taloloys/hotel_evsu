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
        // 1. Add prefix_code to facilities table if not exists
        Schema::table('facilities', function (Blueprint $table) {
            $table->string('prefix_code', 30)->nullable()->after('name');
        });

        // 2. Update facility_reservations to support facility_set_id and nullable facility_id
        Schema::table('facility_reservations', function (Blueprint $table) {
            $table->unsignedBigInteger('facility_id')->nullable()->change();
            $table->foreignId('facility_set_id')->nullable()->after('facility_id')
                ->constrained('facility_sets', 'facility_set_id')
                ->nullOnDelete();
        });

        // 3. Create facility_reservation_facility table for exact traceability of reserved facilities
        Schema::create('facility_reservation_facility', function (Blueprint $table) {
            $table->foreignId('reservation_id')->constrained('facility_reservations', 'reservation_id')->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained('facilities', 'facility_id')->cascadeOnDelete();
            $table->primary(['reservation_id', 'facility_id']);
        });

        // 4. Backfill existing reservations into the facility_reservation_facility table
        $existing = DB::table('facility_reservations')
            ->whereNotNull('facility_id')
            ->select('reservation_id', 'facility_id')
            ->get();

        foreach ($existing as $row) {
            DB::table('facility_reservation_facility')->insertOrIgnore([
                'reservation_id' => $row->reservation_id,
                'facility_id' => $row->facility_id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_reservation_facility');

        Schema::table('facility_reservations', function (Blueprint $table) {
            $table->dropForeign(['facility_set_id']);
            $table->dropColumn('facility_set_id');
            $table->unsignedBigInteger('facility_id')->nullable(false)->change();
        });

        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn('prefix_code');
        });
    }
};
