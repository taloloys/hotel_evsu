<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ShiftSchedule;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class ShiftScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = ShiftSchedule::with(['user', 'actualShift']);

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('shift_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('shift_date', '<=', $request->date_to);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $schedules = $query->orderByDesc('shift_date')
            ->orderBy('scheduled_start_time')
            ->get();

        $users = User::where('is_active', true)
            ->with('role')
            ->get();

        return view('admin.shift-schedules.index', [
            'schedules' => $schedules,
            'users' => $users,
            'filters' => $request->only(['date_from', 'date_to', 'user_id', 'status']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,user_id'],
            'shift_name' => ['required', 'string', 'max:100'],
            'shift_date' => ['required', 'date'],
            'scheduled_start_time' => ['required', 'date_format:H:i'],
            'scheduled_end_time' => ['required', 'date_format:H:i', 'after:scheduled_start_time'],
            'notes' => ['nullable', 'string'],
        ]);

        $schedule = ShiftSchedule::create([
            'user_id' => $validated['user_id'],
            'shift_name' => $validated['shift_name'],
            'shift_date' => $validated['shift_date'],
            'scheduled_start_time' => $validated['scheduled_start_time'],
            'scheduled_end_time' => $validated['scheduled_end_time'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'SCHEDULED',
        ]);

        $user = User::findOrFail($validated['user_id']);
        ActivityLog::log(
            'SHIFT_SCHEDULE_CREATED',
            "Scheduled shift '{$schedule->shift_name}' for {$user->full_name} on {$schedule->shift_date->format('Y-m-d')}."
        );

        return redirect()
            ->route('admin.shift-schedules')
            ->with('success', 'Shift schedule created successfully.');
    }

    public function update(Request $request, ShiftSchedule $schedule)
    {
        if ($schedule->status === 'COMPLETED' || $schedule->status === 'ACTIVE') {
            return back()->withErrors(['status' => 'Cannot modify active or completed shift schedules.']);
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,user_id'],
            'shift_name' => ['required', 'string', 'max:100'],
            'shift_date' => ['required', 'date'],
            'scheduled_start_time' => ['required', 'date_format:H:i'],
            'scheduled_end_time' => ['required', 'date_format:H:i', 'after:scheduled_start_time'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldUser = $schedule->user;
        $newUser = User::findOrFail($validated['user_id']);

        $schedule->update([
            'user_id' => $validated['user_id'],
            'shift_name' => $validated['shift_name'],
            'shift_date' => $validated['shift_date'],
            'scheduled_start_time' => $validated['scheduled_start_time'],
            'scheduled_end_time' => $validated['scheduled_end_time'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $logMsg = "Updated shift schedule '{$schedule->shift_name}' for {$newUser->full_name} on {$schedule->shift_date->format('Y-m-d')}.";
        if ($oldUser->user_id !== $newUser->user_id) {
            $logMsg .= " Reassigned from {$oldUser->full_name} to {$newUser->full_name}.";
        }

        ActivityLog::log('SHIFT_SCHEDULE_UPDATED', $logMsg);

        return redirect()
            ->route('admin.shift-schedules')
            ->with('success', 'Shift schedule updated successfully.');
    }

    public function destroy(ShiftSchedule $schedule)
    {
        if ($schedule->status !== 'SCHEDULED') {
            return back()->withErrors(['status' => 'Only scheduled shifts that have not started can be deleted.']);
        }

        $userName = $schedule->user->full_name;
        $shiftDate = $schedule->shift_date->format('Y-m-d');
        $shiftName = $schedule->shift_name;

        $schedule->delete();

        ActivityLog::log(
            'SHIFT_SCHEDULE_DELETED',
            "Deleted shift schedule '{$shiftName}' for {$userName} on {$shiftDate}."
        );

        return redirect()
            ->route('admin.shift-schedules')
            ->with('success', 'Shift schedule deleted successfully.');
    }

    public function report(ShiftSchedule $schedule)
    {
        $schedule->load(['user', 'actualShift']);
        $shift = $schedule->actualShift;

        $transactions = collect();
        $salesSummary = [
            'total_charges' => 0.00,
            'total_payments' => 0.00,
            'cash_payments' => 0.00,
            'card_payments' => 0.00,
            'check_payments' => 0.00,
            'by_category' => [],
        ];

        if ($shift) {
            $transactions = Transaction::where('shift_id', $shift->shift_id)
                ->with(['folio.guest', 'chargeCode'])
                ->orderBy('timestamp')
                ->get();

            $salesSummary['total_charges'] = $transactions->sum('charge_amount');
            $salesSummary['total_payments'] = $transactions->sum('credit_amount');

            $salesSummary['cash_payments'] = $transactions->where('payment_method', 'CASH')->sum('credit_amount');
            $salesSummary['card_payments'] = $transactions->where('payment_method', 'CREDIT_CARD')->sum('credit_amount');
            $salesSummary['check_payments'] = $transactions->where('payment_method', 'CHECK')->sum('credit_amount');

            // Group charges by category
            foreach ($transactions->where('charge_amount', '>', 0) as $tx) {
                $category = $tx->chargeCode?->category ?? 'OTHER';
                if (! isset($salesSummary['by_category'][$category])) {
                    $salesSummary['by_category'][$category] = 0.00;
                }
                $salesSummary['by_category'][$category] += $tx->charge_amount;
            }
        }

        return view('admin.shift-schedules.report', [
            'schedule' => $schedule,
            'shift' => $shift,
            'transactions' => $transactions,
            'salesSummary' => $salesSummary,
        ]);
    }
}
