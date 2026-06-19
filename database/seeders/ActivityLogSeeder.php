<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('username', 'admin')->first();
        $frontdesk = User::where('username', 'frontdesk')->first();
        $accounting = User::where('username', 'accounting')->first();
        $cafeteria = User::where('username', 'cafeteria')->first();

        // If no users exist, do not seed logs
        if (! $admin || ! $frontdesk || ! $accounting || ! $cafeteria) {
            return;
        }

        $logs = [
            // Admin logs (Critical settings, user creation, room adjustments)
            [
                'user_id' => $admin->user_id,
                'action_type' => 'LOGIN',
                'description' => 'Administrator logged into the system.',
                'days_ago' => 6,
            ],
            [
                'user_id' => $admin->user_id,
                'action_type' => 'ROOM_MODIFIED',
                'description' => 'Created room 101 with standard configuration.',
                'days_ago' => 6,
            ],
            [
                'user_id' => $admin->user_id,
                'action_type' => 'ROOM_MODIFIED',
                'description' => 'Updated room 102 base rate to $150.00.',
                'days_ago' => 6,
            ],
            [
                'user_id' => $admin->user_id,
                'action_type' => 'ROOM_MODIFIED',
                'description' => 'Enabled charge code [100] (ROOM CHARGE).',
                'days_ago' => 5,
            ],

            // Front Desk logs (Reservations, Check ins)
            [
                'user_id' => $frontdesk->user_id,
                'action_type' => 'LOGIN',
                'description' => 'Front Desk agent logged in.',
                'days_ago' => 4,
            ],
            [
                'user_id' => $frontdesk->user_id,
                'action_type' => 'RESERVATION_CREATE',
                'description' => 'Created reservation for guest John Doe, Folio #RSV-2026001 (Room 101).',
                'days_ago' => 4,
            ],
            [
                'user_id' => $frontdesk->user_id,
                'action_type' => 'RESERVATION_CREATE',
                'description' => 'Created reservation for guest Jane Smith, Folio #RSV-2026002 (Room 102).',
                'days_ago' => 3,
            ],
            [
                'user_id' => $frontdesk->user_id,
                'action_type' => 'CHECK_IN',
                'description' => 'Checked in guest John Doe (Booking #1, Room 101).',
                'days_ago' => 2,
            ],
            [
                'user_id' => $frontdesk->user_id,
                'action_type' => 'ADD_CHARGE',
                'description' => 'Added Room Charge of $120.00 to Folio #RSV-2026001.',
                'days_ago' => 2,
            ],
            [
                'user_id' => $frontdesk->user_id,
                'action_type' => 'PRINT_FOLIO',
                'description' => 'Printed guest folio for Room 101 (Folio #RSV-2026001).',
                'days_ago' => 2,
            ],
            [
                'user_id' => $frontdesk->user_id,
                'action_type' => 'CHECK_IN',
                'description' => 'Checked out guest John Doe (Room 101 status set to CLEANING).',
                'days_ago' => 1,
            ],
            [
                'user_id' => $frontdesk->user_id,
                'action_type' => 'ROOM_MODIFIED',
                'description' => 'Housekeeping completed: Room 101 marked AVAILABLE.',
                'days_ago' => 1,
            ],
            [
                'user_id' => $frontdesk->user_id,
                'action_type' => 'CLOSE_SHIFT',
                'description' => 'Closed Front Desk morning shift (Shift #12). Total sales cash out: $120.00.',
                'days_ago' => 1,
            ],

            // Accounting logs
            [
                'user_id' => $accounting->user_id,
                'action_type' => 'LOGIN',
                'description' => 'Accounting agent logged in.',
                'days_ago' => 2,
            ],
            [
                'user_id' => $accounting->user_id,
                'action_type' => 'ADD_CHARGE',
                'description' => 'Adjusted charge for Folio #RSV-2026001: Waived early check-in fee.',
                'days_ago' => 2,
            ],
            [
                'user_id' => $accounting->user_id,
                'action_type' => 'CLOSE_SHIFT',
                'description' => 'Audited Shift #12 cashier reports and matched bank deposits.',
                'days_ago' => 1,
            ],

            // Cafeteria logs
            [
                'user_id' => $cafeteria->user_id,
                'action_type' => 'LOGIN',
                'description' => 'Cafeteria cashier logged in.',
                'days_ago' => 3,
            ],
            [
                'user_id' => $cafeteria->user_id,
                'action_type' => 'ADD_CHARGE',
                'description' => 'Added Coffee Shop Charge of $15.50 (Invoice #CS-8902) to Folio #RSV-2026002.',
                'days_ago' => 3,
            ],
            [
                'user_id' => $cafeteria->user_id,
                'action_type' => 'CLOSE_SHIFT',
                'description' => 'Closed Cafeteria shift. Total POS sales: $15.50.',
                'days_ago' => 3,
            ],
        ];

        foreach ($logs as $logData) {
            $timestamp = Carbon::now()->subDays($logData['days_ago'])->subHours(rand(1, 12))->subMinutes(rand(1, 59));
            ActivityLog::create([
                'user_id' => $logData['user_id'],
                'action_type' => $logData['action_type'],
                'description' => $logData['description'],
                'timestamp' => $timestamp,
            ]);
        }
    }
}
