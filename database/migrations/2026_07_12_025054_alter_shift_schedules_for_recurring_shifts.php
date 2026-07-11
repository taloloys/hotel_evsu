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
        Schema::table('shift_schedules', function (Blueprint $table) {
            $table->boolean('is_monday')->default(false)->after('shift_name');
            $table->boolean('is_tuesday')->default(false)->after('is_monday');
            $table->boolean('is_wednesday')->default(false)->after('is_tuesday');
            $table->boolean('is_thursday')->default(false)->after('is_wednesday');
            $table->boolean('is_friday')->default(false)->after('is_thursday');
            $table->boolean('is_saturday')->default(false)->after('is_friday');
            $table->boolean('is_sunday')->default(false)->after('is_saturday');
            $table->boolean('is_active')->default(true)->after('is_sunday');
        });

        // Convert existing shift_date to the corresponding boolean flag
        $schedules = DB::table('shift_schedules')->get();
        foreach ($schedules as $schedule) {
            if ($schedule->shift_date) {
                $dayOfWeek = date('N', strtotime($schedule->shift_date)); // 1 (Mon) to 7 (Sun)
                DB::table('shift_schedules')
                    ->where('id', $schedule->id)
                    ->update([
                        'is_monday' => $dayOfWeek == 1,
                        'is_tuesday' => $dayOfWeek == 2,
                        'is_wednesday' => $dayOfWeek == 3,
                        'is_thursday' => $dayOfWeek == 4,
                        'is_friday' => $dayOfWeek == 5,
                        'is_saturday' => $dayOfWeek == 6,
                        'is_sunday' => $dayOfWeek == 7,
                    ]);
            }
        }

        Schema::table('shift_schedules', function (Blueprint $table) {
            $table->dropColumn(['shift_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_schedules', function (Blueprint $table) {
            $table->date('shift_date')->nullable();
            $table->enum('status', ['SCHEDULED', 'ACTIVE', 'COMPLETED', 'MISSED'])->default('SCHEDULED');
        });

        // We can't perfectly recover shift_date, so we'll just set it to today for active schedules
        DB::table('shift_schedules')->update(['shift_date' => now()->toDateString()]);

        Schema::table('shift_schedules', function (Blueprint $table) {
            $table->dropColumn([
                'is_monday', 'is_tuesday', 'is_wednesday', 'is_thursday',
                'is_friday', 'is_saturday', 'is_sunday', 'is_active',
            ]);
        });
    }
};
