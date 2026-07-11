<?php

namespace App\Listeners;

use App\Models\Shift;
use App\Models\ShiftSchedule;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class ShiftLifecycleListener
{
    /**
     * Handle the user login event.
     */
    public function handleLogin(Login $event): void
    {
        $user = $event->user;

        if (! $user || ! $user->role) {
            return;
        }

        // Only create shifts for FRONT_DESK and CAFETERIA
        if (! in_array($user->role->role_name, ['FRONT_DESK', 'CAFETERIA'])) {
            return;
        }

        // Close any orphaned active shifts from previous days or any lingering ones
        $activeShifts = Shift::where('user_id', $user->user_id)
            ->whereNull('end_time')
            ->get();

        foreach ($activeShifts as $activeShift) {
            $activeShift->update(['end_time' => now()]);
        }

        // Determine the current day of the week as a column name
        $dayOfWeek = strtolower(now()->englishDayOfWeek); // e.g., 'monday', 'tuesday'
        $columnName = 'is_'.$dayOfWeek;

        // Find an active scheduled shift for today
        $schedule = ShiftSchedule::where('user_id', $user->user_id)
            ->where('is_active', true)
            ->where($columnName, true)
            ->first();

        // Create a new shift for the current session
        Shift::create([
            'user_id' => $user->user_id,
            'schedule_id' => $schedule ? $schedule->id : null,
            'start_time' => now(),
        ]);
    }

    /**
     * Handle the user logout event.
     */
    public function handleLogout(Logout $event): void
    {
        $user = $event->user;

        if (! $user) {
            return;
        }

        $activeShift = Shift::where('user_id', $user->user_id)
            ->whereNull('end_time')
            ->first();

        if ($activeShift) {
            $activeShift->update(['end_time' => now()]);
        }
    }
}
