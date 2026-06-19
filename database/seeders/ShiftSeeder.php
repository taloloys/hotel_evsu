<?php

namespace Database\Seeders;

use App\Models\Shift;
use App\Models\ShiftSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $frontdesk = User::where('username', 'frontdesk')->first();
        $cafeteria = User::where('username', 'cafeteria')->first();

        if (! $frontdesk || ! $cafeteria) {
            return;
        }

        $today = Carbon::now();

        // 1. Past completed shifts for front desk
        for ($i = 3; $i >= 1; $i--) {
            $date = $today->copy()->subDays($i)->toDateString();
            $schedule = ShiftSchedule::create([
                'user_id' => $frontdesk->user_id,
                'shift_name' => 'Morning Frontdesk',
                'shift_date' => $date,
                'scheduled_start_time' => '06:00:00',
                'scheduled_end_time' => '14:00:00',
                'notes' => 'Standard morning shift for Front Desk agent.',
                'status' => 'COMPLETED',
            ]);

            // Create actual shift session
            Shift::create([
                'user_id' => $frontdesk->user_id,
                'schedule_id' => $schedule->id,
                'start_time' => Carbon::parse("{$date} 05:58:00"),
                'end_time' => Carbon::parse("{$date} 14:02:00"),
            ]);
        }

        // 2. Past completed shifts for cafeteria
        for ($i = 2; $i >= 1; $i--) {
            $date = $today->copy()->subDays($i)->toDateString();
            $schedule = ShiftSchedule::create([
                'user_id' => $cafeteria->user_id,
                'shift_name' => 'Afternoon Coffee Shop',
                'shift_date' => $date,
                'scheduled_start_time' => '14:00:00',
                'scheduled_end_time' => '22:00:00',
                'notes' => 'Standard cafeteria POS shift.',
                'status' => 'COMPLETED',
            ]);

            Shift::create([
                'user_id' => $cafeteria->user_id,
                'schedule_id' => $schedule->id,
                'start_time' => Carbon::parse("{$date} 13:55:00"),
                'end_time' => Carbon::parse("{$date} 22:05:00"),
            ]);
        }

        // 3. Active shift today for frontdesk
        $todayDate = $today->toDateString();
        $todaySchedule = ShiftSchedule::create([
            'user_id' => $frontdesk->user_id,
            'shift_name' => 'Morning Frontdesk',
            'shift_date' => $todayDate,
            'scheduled_start_time' => '06:00:00',
            'scheduled_end_time' => '14:00:00',
            'notes' => 'Today\'s active shift.',
            'status' => 'ACTIVE',
        ]);

        Shift::create([
            'user_id' => $frontdesk->user_id,
            'schedule_id' => $todaySchedule->id,
            'start_time' => Carbon::parse("{$todayDate} 06:02:00"),
            'end_time' => null, // active
        ]);

        // 4. Future scheduled shifts
        ShiftSchedule::create([
            'user_id' => $frontdesk->user_id,
            'shift_name' => 'Morning Frontdesk',
            'shift_date' => $today->copy()->addDay()->toDateString(),
            'scheduled_start_time' => '06:00:00',
            'scheduled_end_time' => '14:00:00',
            'notes' => 'Scheduled for tomorrow.',
            'status' => 'SCHEDULED',
        ]);

        ShiftSchedule::create([
            'user_id' => $cafeteria->user_id,
            'shift_name' => 'Afternoon Coffee Shop',
            'shift_date' => $today->copy()->addDay()->toDateString(),
            'scheduled_start_time' => '14:00:00',
            'scheduled_end_time' => '22:00:00',
            'notes' => 'Scheduled for tomorrow afternoon.',
            'status' => 'SCHEDULED',
        ]);
    }
}
