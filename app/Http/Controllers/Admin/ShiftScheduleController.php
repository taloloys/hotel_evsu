<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ShiftSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class ShiftScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = ShiftSchedule::with(['user']);

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by active status
        if ($request->filled('status')) {
            $isActive = $request->status === 'ACTIVE';
            $query->where('is_active', $isActive);
        }

        $schedules = $query->orderBy('scheduled_start_time')->get();

        $users = User::where('is_active', true)
            ->with('role')
            ->get();

        return view('admin.shift-schedules.index', [
            'schedules' => $schedules,
            'users' => $users,
            'filters' => $request->only(['user_id', 'status']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,user_id'],
            'shift_name' => ['required', 'string', 'max:100'],
            'scheduled_start_time' => ['required', 'date_format:H:i'],
            'scheduled_end_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string'],
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $days = collect($validated['days'] ?? []);

        $schedule = ShiftSchedule::create([
            'user_id' => $validated['user_id'],
            'shift_name' => $validated['shift_name'],
            'is_monday' => $days->contains('monday'),
            'is_tuesday' => $days->contains('tuesday'),
            'is_wednesday' => $days->contains('wednesday'),
            'is_thursday' => $days->contains('thursday'),
            'is_friday' => $days->contains('friday'),
            'is_saturday' => $days->contains('saturday'),
            'is_sunday' => $days->contains('sunday'),
            'is_active' => $request->has('is_active'),
            'scheduled_start_time' => $validated['scheduled_start_time'],
            'scheduled_end_time' => $validated['scheduled_end_time'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $user = User::findOrFail($validated['user_id']);
        ActivityLog::log(
            'SHIFT_SCHEDULE_CREATED',
            "Created recurring shift schedule '{$schedule->shift_name}' for {$user->full_name}."
        );

        return redirect()
            ->route('admin.shift-schedules')
            ->with('success', 'Recurring shift schedule created successfully.');
    }

    public function update(Request $request, ShiftSchedule $schedule)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,user_id'],
            'shift_name' => ['required', 'string', 'max:100'],
            'scheduled_start_time' => ['required', 'date_format:H:i'],
            'scheduled_end_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string'],
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $oldUser = $schedule->user;
        $newUser = User::findOrFail($validated['user_id']);
        $days = collect($validated['days'] ?? []);

        $schedule->update([
            'user_id' => $validated['user_id'],
            'shift_name' => $validated['shift_name'],
            'is_monday' => $days->contains('monday'),
            'is_tuesday' => $days->contains('tuesday'),
            'is_wednesday' => $days->contains('wednesday'),
            'is_thursday' => $days->contains('thursday'),
            'is_friday' => $days->contains('friday'),
            'is_saturday' => $days->contains('saturday'),
            'is_sunday' => $days->contains('sunday'),
            'is_active' => $request->has('is_active'),
            'scheduled_start_time' => $validated['scheduled_start_time'],
            'scheduled_end_time' => $validated['scheduled_end_time'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $logMsg = "Updated recurring shift schedule '{$schedule->shift_name}' for {$newUser->full_name}.";
        if ($oldUser->user_id !== $newUser->user_id) {
            $logMsg .= " Reassigned from {$oldUser->full_name} to {$newUser->full_name}.";
        }

        ActivityLog::log('SHIFT_SCHEDULE_UPDATED', $logMsg);

        return redirect()
            ->route('admin.shift-schedules')
            ->with('success', 'Recurring shift schedule updated successfully.');
    }

    public function destroy(ShiftSchedule $schedule)
    {
        $userName = $schedule->user->full_name;
        $shiftName = $schedule->shift_name;

        $schedule->delete();

        ActivityLog::log(
            'SHIFT_SCHEDULE_DELETED',
            "Deleted recurring shift schedule '{$shiftName}' for {$userName}."
        );

        return redirect()
            ->route('admin.shift-schedules')
            ->with('success', 'Shift schedule deleted successfully.');
    }

    // Report method was previously for a single specific shift. Since schedules are now templates,
    // this report would need to be rewritten or removed, depending on requirements.
    // For now, I will remove it as the previous single-shift relationship `actualShift` no longer exists.
}
