<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $dateFrom = $request->input('date_from', Carbon::now()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->toDateString());
        $reportType = $request->input('report_type', 'ALL');

        $needsArchive = Carbon::parse($dateFrom)->diffInDays(Carbon::now()) > 365;
        $transactionColumns = ['transaction_id', 'folio_id', 'charge_code', 'shift_id', 'user_id', 'transaction_date', 'charge_number', 'payment_method', 'reference_notes', 'charge_amount', 'credit_amount', 'department', 'timestamp'];

        // 1. Profit & Loss data
        $revenue = Transaction::whereBetween('transaction_date', [$dateFrom, $dateTo])->sum('charge_amount');
        if ($needsArchive) {
            $revenue += DB::table('archived_transactions')->whereBetween('transaction_date', [$dateFrom, $dateTo])->sum('charge_amount');
        }

        $expenses = Expense::where('status', 'APPROVED')->whereBetween('expense_date', [$dateFrom, $dateTo])->sum('amount');
        if ($needsArchive) {
            $expenses += DB::table('archived_expenses')->where('status', 'APPROVED')->whereBetween('expense_date', [$dateFrom, $dateTo])->sum('amount');
        }

        $netProfit = $revenue - $expenses;

        // 2. Cash Flow data
        $cashIn = Transaction::whereBetween('transaction_date', [$dateFrom, $dateTo])->sum('credit_amount');
        if ($needsArchive) {
            $cashIn += DB::table('archived_transactions')->whereBetween('transaction_date', [$dateFrom, $dateTo])->sum('credit_amount');
        }

        $cashOut = $expenses; // Assuming approved operational expenses represent cash outflow
        $netCashFlow = $cashIn - $cashOut;

        // 3. Revenue Breakdown by category
        $revenueGroupedQuery = Transaction::where('charge_amount', '>', 0)
            ->whereBetween('transaction_date', [$dateFrom, $dateTo]);

        if ($needsArchive) {
            $archivedRevenue = DB::table('archived_transactions')
                ->select($transactionColumns)
                ->where('charge_amount', '>', 0)
                ->whereBetween('transaction_date', [$dateFrom, $dateTo]);

            $revenueGroupedQuery = clone $revenueGroupedQuery; // Clone to avoid modifying the original query if it was used elsewhere, though it's not.
            $revenueGroupedQuery = $revenueGroupedQuery->select($transactionColumns)->unionAll($archivedRevenue);
        }

        $revenueGrouped = $revenueGroupedQuery->with('chargeCode')
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
        $transactionsQuery = Transaction::whereBetween('transaction_date', [$dateFrom, $dateTo]);

        if ($needsArchive) {
            $archivedTxs = DB::table('archived_transactions')
                ->select($transactionColumns)
                ->whereBetween('transaction_date', [$dateFrom, $dateTo]);

            $transactionsQuery = $transactionsQuery->select($transactionColumns)->unionAll($archivedTxs);
        }

        $transactions = $transactionsQuery->with(['folio.guest', 'chargeCode', 'user'])
            ->orderBy('timestamp', 'desc')
            ->paginate(20)->withQueryString();

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
