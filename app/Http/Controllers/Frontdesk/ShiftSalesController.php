<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\ChargeCode;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class ShiftSalesController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view-shift-sales');

        $isAdmin = auth()->user()?->role?->role_name === 'ADMIN';

        if ($isAdmin) {
            $users = User::where('is_active', true)->with('role')->get();
        } else {
            $users = User::where('user_id', auth()->id())->with('role')->get();
        }
        $chargeCodes = ChargeCode::where('is_active', true)->orderBy('charge_code')->get();

        $query = Transaction::query()
            ->with(['user', 'chargeCode', 'folio.guest']);

        $dateFrom = $request->input('date_from');
        $dateUntil = $request->input('date_until');

        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        } else {
            if (! $isAdmin) {
                $targetUserId = auth()->id();
            } else {
                $targetUserId = $request->input('employee_id');
            }

            if ($targetUserId) {
                $query->where(function ($q) use ($targetUserId) {
                    $q->where('user_id', $targetUserId)
                      ->orWhereHas('shift', function ($sub) use ($targetUserId) {
                          $sub->where('user_id', $targetUserId);
                      });
                });
            }

            if ($dateFrom) {
                $query->whereDate('transaction_date', '>=', $dateFrom);
            }
            if ($dateUntil) {
                $query->whereDate('transaction_date', '<=', $dateUntil);
            }

            // Report Type (Category) filter
            if ($request->filled('report_type') && $request->report_type !== 'all') {
                $query->whereHas('chargeCode', function ($sub) use ($request) {
                    if ($request->report_type === 'hotel') {
                        $sub->whereIn('category', ['HOTEL', 'TAX_SERVICE']);
                    } elseif ($request->report_type === 'restaurant') {
                        $sub->where('category', 'RESTAURANT');
                    }
                });
            }

            // Charge Code From/Until range filters
            if ($request->filled('charge_code_from')) {
                $query->where('charge_code', '>=', $request->charge_code_from);
            }
            if ($request->filled('charge_code_until')) {
                $query->where('charge_code', '<=', $request->charge_code_until);
            }
        }

        $transactions = collect();
        $totals = [
            'charges' => 0.00,
            'payments' => 0.00,
            'total_charges' => 0.00,
            'room_charges' => 0.00,
            'additional_charges' => 0.00,
            'checkin_count' => 0,
            'shift_expenses' => 0.00,
            'net_income' => 0.00,
        ];

        // Only run report if form has been submitted
        $hasSearched = $request->has('report_type') || $request->has('date_from') || $request->has('shift_id') || $request->has('employee_id') || $request->has('charge_code_from') || $request->has('charge_code_until');

        if ($hasSearched) {
            $transactions = $query->orderBy('timestamp')->get();
            $totals['charges'] = $transactions->sum('charge_amount');
            $totals['payments'] = $transactions->sum('credit_amount');
            $totals['total_charges'] = $transactions->sum('charge_amount');
            $totals['room_charges'] = $transactions->filter(fn ($tx) => in_array($tx->chargeCode?->category, ['HOTEL', 'TAX_SERVICE']))
                ->sum('charge_amount');
            $totals['additional_charges'] = $transactions->filter(fn ($tx) => ! in_array($tx->chargeCode?->category, ['HOTEL', 'TAX_SERVICE']))
                ->sum('charge_amount');
            $totals['checkin_count'] = $transactions->pluck('folio_id')->filter()->unique()->count();

            // Calculate expenses if filtering by shift or date
            $expenseQuery = Expense::where('funding_source', 'FRONT DESK')->where('status', 'APPROVED');
            if ($request->filled('shift_id')) {
                $selectedShift = Shift::find($request->shift_id);
                if ($selectedShift) {
                    $expenseQuery->whereBetween('created_at', [$selectedShift->start_time, $selectedShift->end_time ?? Carbon::now()]);
                }
            } else {
                if ($dateFrom) {
                    $expenseQuery->whereDate('created_at', '>=', $dateFrom);
                }
                if ($dateUntil) {
                    $expenseQuery->whereDate('created_at', '<=', $dateUntil);
                }
            }
            $totals['shift_expenses'] = $expenseQuery->sum('amount');

            $totals['net_income'] = $totals['payments'] - $totals['total_charges'] - $totals['shift_expenses'];
        }

        $activeShift = Shift::where('user_id', auth()->id())
            ->whereNull('end_time')
            ->first();

        $shiftsQuery = Shift::with(['user', 'schedule'])
            ->orderByDesc('shift_id');

        if (! $isAdmin) {
            $shiftsQuery->where('user_id', auth()->id());
        }

        $shiftsForSelector = $shiftsQuery->get();

        $selectedShift = null;
        if ($request->filled('shift_id')) {
            $selectedShift = Shift::with(['user', 'schedule'])->find($request->shift_id);
            if ($selectedShift && ! $isAdmin && $selectedShift->user_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }
        }

        return view('frontdesk.shift-sales.index', [
            'users' => $users,
            'chargeCodes' => $chargeCodes,
            'transactions' => $transactions,
            'totals' => $totals,
            'hasSearched' => $hasSearched,
            'filters' => $request->all(),
            'activeShift' => $activeShift,
            'isAdmin' => $isAdmin,
            'shiftsForSelector' => $shiftsForSelector,
            'selectedShift' => $selectedShift,
        ]);
    }

    public function show(Shift $shift)
    {
        Gate::authorize('view-shift-sales');

        $isAdmin = auth()->user()?->role?->role_name === 'ADMIN';

        if (! $isAdmin && $shift->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $transactions = Transaction::where('shift_id', $shift->shift_id)
            ->with(['user', 'chargeCode', 'folio.guest'])
            ->orderBy('timestamp')
            ->get();

        $totals = [
            'charges' => $transactions->sum('charge_amount'),
            'payments' => $transactions->sum('credit_amount'),
            'total_charges' => $transactions->sum('charge_amount'),
            'room_charges' => $transactions->filter(fn ($tx) => in_array($tx->chargeCode?->category, ['HOTEL', 'TAX_SERVICE']))
                ->sum('charge_amount'),
            'additional_charges' => $transactions->filter(fn ($tx) => ! in_array($tx->chargeCode?->category, ['HOTEL', 'TAX_SERVICE']))
                ->sum('charge_amount'),
            'checkin_count' => $transactions->pluck('folio_id')->filter()->unique()->count(),
            'shift_expenses' => Expense::where('funding_source', 'FRONT DESK')
                ->where('status', 'APPROVED')
                ->whereBetween('created_at', [$shift->start_time, $shift->end_time ?? Carbon::now()])
                ->sum('amount'),
            'net_income' => 0.00, // calculated below
        ];
        $totals['net_income'] = $totals['payments'] - $totals['total_charges'] - $totals['shift_expenses'];

        return view('frontdesk.shift-sales.show', [
            'shift' => $shift,
            'transactions' => $transactions,
            'totals' => $totals,
        ]);
    }
}
