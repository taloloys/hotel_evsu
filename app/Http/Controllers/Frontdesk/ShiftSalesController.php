<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\ChargeCode;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShiftSalesController extends Controller
{
    public function index(Request $request): View
    {
        $isAdmin = auth()->user()?->role?->role_name === 'ADMIN';
        $currentUserId = auth()->id();

        // Available employees for the selector
        if ($isAdmin) {
            $users = User::where('is_active', true)->with('role')->orderBy('full_name')->get();
        } else {
            $users = User::where('user_id', $currentUserId)->with('role')->get();
        }

        $chargeCodes = ChargeCode::where('is_active', true)->orderBy('charge_code')->get();

        // Determine the target employee for the report
        $targetUserId = null;
        if ($isAdmin && $request->filled('employee_id')) {
            $targetUserId = (int) $request->employee_id;
        } elseif (! $isAdmin) {
            $targetUserId = $currentUserId;
        }

        // Load shifts for the shift selector (own shifts for staff, filtered by employee for admin)
        $shiftsForSelector = Shift::query()
            ->with(['user', 'schedule'])
            ->when($targetUserId, fn ($q) => $q->where('user_id', $targetUserId))
            ->when(! $isAdmin && ! $targetUserId, fn ($q) => $q->where('user_id', $currentUserId))
            ->orderByDesc('start_time')
            ->limit(50)
            ->get();

        // Active shift for the current user (quick-select shortcut)
        $activeShift = Shift::where('user_id', $currentUserId)
            ->whereNull('end_time')
            ->first();

        $transactions = collect();
        $selectedShift = null;
        $totals = [
            'room_charges' => 0.00,
            'additional_charges' => 0.00,
            'payments' => 0.00,
            'total_charges' => 0.00,
            'net_income' => 0.00,
            'checkin_count' => 0,
        ];

        $hasSearched = $request->has('shift_id') || $request->has('report_type') || $request->has('date_from');

        if ($hasSearched) {
            $query = Transaction::query()->with(['user', 'chargeCode', 'folio.guest']);

            // --- Filter mode: by specific shift ---
            if ($request->filled('shift_id')) {
                $selectedShift = Shift::with(['user', 'schedule'])->find($request->shift_id);

                if ($selectedShift) {
                    // Security: non-admins may only view their own shifts
                    if (! $isAdmin && $selectedShift->user_id !== $currentUserId) {
                        abort(403);
                    }

                    $query->where('shift_id', $selectedShift->shift_id);
                }
            } else {
                // --- Filter mode: by date range ---
                if (! $isAdmin) {
                    $query->where('user_id', $currentUserId);
                } elseif ($request->filled('employee_id')) {
                    $query->where('user_id', $request->employee_id);
                }

                if ($request->filled('date_from')) {
                    $query->whereDate('transaction_date', '>=', $request->date_from);
                }

                if ($request->filled('date_until')) {
                    $query->whereDate('transaction_date', '<=', $request->date_until);
                }

                // Category filter
                if ($request->filled('report_type') && $request->report_type !== 'all') {
                    if ($request->report_type === 'hotel') {
                        $query->whereHas('chargeCode', fn ($q) => $q->whereIn('category', ['HOTEL', 'TAX_SERVICE']));
                    } elseif ($request->report_type === 'restaurant') {
                        $query->whereHas('chargeCode', fn ($q) => $q->where('category', 'RESTAURANT'));
                    }
                }

                // Charge code range filter
                if ($request->filled('charge_code_from')) {
                    $query->where('charge_code', '>=', $request->charge_code_from);
                }

                if ($request->filled('charge_code_until')) {
                    $query->where('charge_code', '<=', $request->charge_code_until);
                }
            }

            $transactions = $query->orderBy('timestamp')->get();

            // --- Compute breakdowns ---
            foreach ($transactions as $tx) {
                $category = $tx->chargeCode?->category;

                if ($tx->charge_amount > 0) {
                    if ($category === 'HOTEL' || $category === 'TAX_SERVICE') {
                        $totals['room_charges'] += $tx->charge_amount;
                    } else {
                        $totals['additional_charges'] += $tx->charge_amount;
                    }
                }

                if ($tx->credit_amount > 0) {
                    $totals['payments'] += $tx->credit_amount;
                }
            }

            $totals['total_charges'] = $totals['room_charges'] + $totals['additional_charges'];
            $totals['net_income'] = $totals['total_charges'] - $totals['payments'];

            // Count distinct folios that had a HOTEL/TAX_SERVICE charge during this period
            // as a proxy for check-ins processed by this employee
            $totals['checkin_count'] = $transactions
                ->filter(fn ($tx) => in_array($tx->chargeCode?->category, ['HOTEL', 'TAX_SERVICE']) && $tx->charge_amount > 0)
                ->pluck('folio_id')
                ->unique()
                ->count();
        }

        return view('frontdesk.shift-sales.index', [
            'users' => $users,
            'chargeCodes' => $chargeCodes,
            'transactions' => $transactions,
            'totals' => $totals,
            'hasSearched' => $hasSearched,
            'filters' => $request->all(),
            'shiftsForSelector' => $shiftsForSelector,
            'selectedShift' => $selectedShift,
            'activeShift' => $activeShift,
            'isAdmin' => $isAdmin,
        ]);
    }

    /**
     * Display a single shift's complete sales report.
     */
    public function show(Shift $shift): View
    {
        $isAdmin = auth()->user()?->role?->role_name === 'ADMIN';
        $currentUserId = auth()->id();

        // Non-admins may only view their own shifts
        if (! $isAdmin && $shift->user_id !== $currentUserId) {
            abort(403);
        }

        $shift->load(['user.role', 'schedule']);

        $transactions = Transaction::query()
            ->with(['chargeCode', 'folio.guest'])
            ->where('shift_id', $shift->shift_id)
            ->orderBy('timestamp')
            ->get();

        $totals = [
            'room_charges' => 0.00,
            'additional_charges' => 0.00,
            'payments' => 0.00,
            'total_charges' => 0.00,
            'net_income' => 0.00,
            'checkin_count' => 0,
        ];

        foreach ($transactions as $tx) {
            $category = $tx->chargeCode?->category;

            if ($tx->charge_amount > 0) {
                if ($category === 'HOTEL' || $category === 'TAX_SERVICE') {
                    $totals['room_charges'] += $tx->charge_amount;
                } else {
                    $totals['additional_charges'] += $tx->charge_amount;
                }
            }

            if ($tx->credit_amount > 0) {
                $totals['payments'] += $tx->credit_amount;
            }
        }

        $totals['total_charges'] = $totals['room_charges'] + $totals['additional_charges'];
        $totals['net_income'] = $totals['total_charges'] - $totals['payments'];

        $totals['checkin_count'] = $transactions
            ->filter(fn ($tx) => in_array($tx->chargeCode?->category, ['HOTEL', 'TAX_SERVICE']) && $tx->charge_amount > 0)
            ->pluck('folio_id')
            ->unique()
            ->count();

        return view('frontdesk.shift-sales.show', [
            'shift' => $shift,
            'transactions' => $transactions,
            'totals' => $totals,
            'isAdmin' => $isAdmin,
        ]);
    }
}
