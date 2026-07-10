<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'all');

        $txQuery = Transaction::query();
        $expenseQuery = Expense::query();
        $receivablesBaseQuery = Transaction::whereHas('folio', function ($query) {
            $query->where('status', 'OPEN');
        });

        if ($filter === 'today') {
            $txQuery->whereDate('timestamp', Carbon::today());
            $expenseQuery->whereDate('expense_date', Carbon::today());
            $receivablesBaseQuery->whereDate('timestamp', Carbon::today());
        } elseif ($filter === 'weekly') {
            $txQuery->whereBetween('timestamp', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            $expenseQuery->whereBetween('expense_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            $receivablesBaseQuery->whereBetween('timestamp', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($filter === 'monthly') {
            $txQuery->whereMonth('timestamp', Carbon::now()->month)->whereYear('timestamp', Carbon::now()->year);
            $expenseQuery->whereMonth('expense_date', Carbon::now()->month)->whereYear('expense_date', Carbon::now()->year);
            $receivablesBaseQuery->whereMonth('timestamp', Carbon::now()->month)->whereYear('timestamp', Carbon::now()->year);
        } elseif ($filter === 'yearly') {
            $txQuery->whereYear('timestamp', Carbon::now()->year);
            $expenseQuery->whereYear('expense_date', Carbon::now()->year);
            $receivablesBaseQuery->whereYear('timestamp', Carbon::now()->year);
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

        // Cash Out: approved expenses
        $cashOut = $approvedExpenses;
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
