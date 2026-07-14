<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'today');

        $txQuery = Transaction::query();
        $expenseQuery = Expense::query();
        $receivablesBaseQuery = Transaction::whereHas('folio', function ($query) {
            $query->where('status', 'OPEN');
        });

        if ($filter === 'today') {
            $start = Carbon::today();
            $end = Carbon::today()->endOfDay();
            $txQuery->whereBetween('timestamp', [$start, $end]);
            $expenseQuery->whereBetween('expense_date', [$start, $end]);
            $receivablesBaseQuery->whereBetween('timestamp', [$start, $end]);
        } elseif ($filter === 'weekly') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
            $txQuery->whereBetween('timestamp', [$start, $end]);
            $expenseQuery->whereBetween('expense_date', [$start, $end]);
            $receivablesBaseQuery->whereBetween('timestamp', [$start, $end]);
        } elseif ($filter === 'monthly') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            $txQuery->whereBetween('timestamp', [$start, $end]);
            $expenseQuery->whereBetween('expense_date', [$start, $end]);
            $receivablesBaseQuery->whereBetween('timestamp', [$start, $end]);
        } elseif ($filter === 'yearly') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
            $txQuery->whereBetween('timestamp', [$start, $end]);
            $expenseQuery->whereBetween('expense_date', [$start, $end]);
            $receivablesBaseQuery->whereBetween('timestamp', [$start, $end]);
        }

        // 1. Core KPIs
        $revenue = (clone $txQuery)->sum('charge_amount');

        $approvedExpenses = (clone $expenseQuery)->where('status', 'APPROVED')->sum('amount');
        $profit = $revenue - $approvedExpenses;

        // Receivables: Total charges - Total credits on OPEN folios
        $totalChargesOpen = (float) (clone $receivablesBaseQuery)->sum('charge_amount');
        $totalCreditsOpen = (float) (clone $receivablesBaseQuery)->sum('credit_amount');
        $receivables = max(0.00, $totalChargesOpen - $totalCreditsOpen);

        // 2. Cash Summary (Today's net flow or total flow)
        // Cash In: payments with method CASH
        $cashIn = (clone $txQuery)->where('payment_method', 'CASH')->sum('credit_amount');

        $cashInCard = (clone $txQuery)->where('payment_method', 'CREDIT_CARD')->sum('credit_amount');

        // Cash Out: approved expenses (from Front Desk)
        $cashOut = (clone $expenseQuery)->where('status', 'APPROVED')->where('funding_source', 'FRONT DESK')->sum('amount');
        $netFlow = ($cashIn + $cashInCard) - $cashOut;

        // 3. Recent Transactions
        $recentTransactions = Transaction::with(['folio.guest', 'chargeCode'])
            ->orderBy('timestamp', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('accounting.dashboard.index', [
            'revenue' => $revenue,
            'profit' => $profit,
            'receivables' => $receivables,
            'expenses' => $approvedExpenses,
            'cashIn' => $cashIn,
            'cashInCard' => $cashInCard,
            'cashOut' => $cashOut,
            'netFlow' => $netFlow,
            'recentTransactions' => $recentTransactions,
            'filter' => $filter,
        ]);
    }
}
