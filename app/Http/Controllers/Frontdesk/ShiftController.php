<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Shift;
use App\Models\ShiftSchedule;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    /**
     * Start/Open a shift session
     */
    public function open(Request $request)
    {
        $userId = auth()->id();

        // Check if shift is already active for this user
        $existing = Shift::where('user_id', $userId)
            ->whereNull('end_time')
            ->first();

        if ($existing) {
            return back()->withErrors(['shift' => 'You already have an active shift session.']);
        }

        $scheduleId = $request->input('schedule_id');
        $schedule = null;

        if ($scheduleId) {
            $schedule = ShiftSchedule::where('user_id', $userId)
                ->where('id', $scheduleId)
                ->first();

            if (! $schedule || ! $schedule->is_active) {
                return back()->withErrors(['shift' => 'Invalid or inactive shift schedule.']);
            }
        }

        // Create shift
        $shift = Shift::create([
            'user_id' => $userId,
            'schedule_id' => $scheduleId,
            'start_time' => Carbon::now(),
            'end_time' => null,
        ]);

        // Status is no longer updated as schedules are recurring

        ActivityLog::log(
            'CHECK_IN',
            "Opened Shift #{$shift->shift_id} for ".auth()->user()->full_name.'.'
        );

        return back()->with('success', 'Shift session started successfully. You can now post transactions.');
    }

    /**
     * Close the current active shift session
     */
    public function close(Request $request)
    {
        $userId = auth()->id();

        // Find active shift
        $shift = Shift::where('user_id', $userId)
            ->whereNull('end_time')
            ->first();

        if (! $shift) {
            return back()->withErrors(['shift' => 'No active shift session found to close.']);
        }

        // Calculate total payments/sales generated during the shift
        $totalPayments = Transaction::where('shift_id', $shift->shift_id)
            ->sum('credit_amount');

        $expenses = \App\Models\Expense::where('user_id', $userId)
            ->where('funding_source', 'FRONT DESK')
            ->where('created_at', '>=', $shift->start_time)
            ->sum('amount');

        // Close shift
        $shift->update([
            'end_time' => Carbon::now(),
        ]);

        // Status is no longer updated as schedules are recurring

        $netCashOut = $totalPayments - $expenses;

        ActivityLog::log(
            'CLOSE_SHIFT',
            "Closed Shift #{$shift->shift_id}. Sales: ₱".number_format($totalPayments, 2).", Expenses: ₱".number_format($expenses, 2).", Net Cash Out: ₱".number_format($netCashOut, 2).'.'
        );

        return back()->with('success', 'Shift session closed successfully.');
    }
}
