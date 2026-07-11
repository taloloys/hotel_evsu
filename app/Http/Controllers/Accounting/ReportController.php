<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $dateFrom = $request->input('date_from', Carbon::now()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->toDateString());
        $reportType = $request->input('report_type', 'ALL');

        // 1. Profit & Loss data
        $revenue = Transaction::whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->sum('charge_amount');

        $expenses = Expense::where('status', 'APPROVED')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->sum('amount');

        $netProfit = $revenue - $expenses;

        // 2. Cash Flow data
        $cashIn = Transaction::whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->sum('credit_amount');

        $cashOut = $expenses; // Assuming approved operational expenses represent cash outflow
        $netCashFlow = $cashIn - $cashOut;

        // 3. Revenue Breakdown by category
        $revenueGrouped = Transaction::where('charge_amount', '>', 0)
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->with('chargeCode')
            ->get()
            ->groupBy(function ($tx) {
                return $tx->chargeCode ? $tx->chargeCode->category : 'OTHER';
            });

        $revenueBreakdown = [
            'ROOMS' => 0.00,
            'RESTAURANT' => 0.00,
            'TAX_SERVICE' => 0.00,
            'OTHER' => 0.00,
        ];

        foreach ($revenueGrouped as $category => $txs) {
            $sum = $txs->sum('charge_amount');
            if ($category === 'HOTEL') {
                $revenueBreakdown['ROOMS'] = $sum;
            } elseif ($category === 'RESTAURANT') {
                $revenueBreakdown['RESTAURANT'] = $sum;
            } elseif ($category === 'TAX_SERVICE') {
                $revenueBreakdown['TAX_SERVICE'] = $sum;
            } else {
                $revenueBreakdown['OTHER'] += $sum;
            }
        }

        $totalRevenueBreakdown = array_sum($revenueBreakdown);

        // 4. Detailed transaction list
        $transactions = Transaction::whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->with(['folio.guest', 'chargeCode', 'user'])
            ->orderBy('timestamp', 'desc')
            ->get();

        return view('accounting.reports.index', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'reportType' => $reportType,
            'revenue' => $revenue,
            'expenses' => $expenses,
            'netProfit' => $netProfit,
            'cashIn' => $cashIn,
            'cashOut' => $cashOut,
            'netCashFlow' => $netCashFlow,
            'revenueBreakdown' => $revenueBreakdown,
            'totalRevenueBreakdown' => $totalRevenueBreakdown,
            'transactions' => $transactions,
        ]);
    }
}
