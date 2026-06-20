<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Transaction;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Core KPIs
        $revenue = Transaction::sum('charge_amount');

        $approvedExpenses = Expense::where('status', 'APPROVED')->sum('amount');
        $profit = $revenue - $approvedExpenses;

        // Receivables: Total charges - Total credits on OPEN folios
        $receivablesQuery = Transaction::whereHas('folio', function ($query) {
            $query->where('status', 'OPEN');
        });

        $totalChargesOpen = (float) $receivablesQuery->sum('charge_amount');
        $totalCreditsOpen = (float) $receivablesQuery->sum('credit_amount');
        $receivables = max(0.00, $totalChargesOpen - $totalCreditsOpen);

        // 2. Cash Summary (Today's net flow or total flow)
        // Cash In: payments with method CASH
        $cashIn = Transaction::where('payment_method', 'CASH')->sum('credit_amount');

        // Cash Out: approved expenses
        $cashOut = $approvedExpenses;
        $netFlow = $cashIn - $cashOut;

        // 3. Recent Transactions
        $recentTransactions = Transaction::with(['folio.guest', 'chargeCode'])
            ->orderBy('timestamp', 'desc')
            ->take(10)
            ->get();

        return view('accounting.dashboard.index', [
            'revenue' => $revenue,
            'profit' => $profit,
            'receivables' => $receivables,
            'expenses' => $approvedExpenses,
            'cashIn' => $cashIn,
            'cashOut' => $cashOut,
            'netFlow' => $netFlow,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
