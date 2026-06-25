<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\ChargeCode;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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

        if (! $isAdmin) {
            $query->where('user_id', auth()->id());
        } elseif ($request->filled('employee_id')) {
            $query->where('user_id', $request->employee_id);
        }

        // Default dates to today if not provided but request is submitted
        $dateFrom = $request->input('date_from');
        $dateUntil = $request->input('date_until');

        if ($dateFrom) {
            $query->whereDate('transaction_date', '>=', $dateFrom);
        }
        if ($dateUntil) {
            $query->whereDate('transaction_date', '<=', $dateUntil);
        }

        // Report Type (Category) filter
        if ($request->filled('report_type') && $request->report_type !== 'all') {
            if ($request->report_type === 'hotel') {
                $query->whereHas('chargeCode', function ($q) {
                    $q->whereIn('category', ['HOTEL', 'TAX_SERVICE']);
                });
            } elseif ($request->report_type === 'restaurant') {
                $query->whereHas('chargeCode', function ($q) {
                    $q->where('category', 'RESTAURANT');
                });
            }
        }

        // Charge Code From/Until range filters
        if ($request->filled('charge_code_from')) {
            $query->where('charge_code', '>=', $request->charge_code_from);
        }
        if ($request->filled('charge_code_until')) {
            $query->where('charge_code', '<=', $request->charge_code_until);
        }

        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        $transactions = collect();
        $totals = [
            'charges' => 0.00,
            'payments' => 0.00,
            'total_charges' => 0.00,
            'room_charges' => 0.00,
            'additional_charges' => 0.00,
            'checkin_count' => 0,
            'net_income' => 0.00,
        ];

        // Only run report if form has been submitted
        $hasSearched = $request->has('report_type') || $request->has('date_from') || $request->has('shift_id');

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
            $totals['net_income'] = $totals['payments'] - $totals['total_charges'];
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
}
