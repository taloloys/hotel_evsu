<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function data(Request $request): JsonResponse
    {
        $filter = $request->query('filter', 'all');

        if ($filter === 'today') {
            $startDate = Carbon::today();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($filter === 'weekly') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        } elseif ($filter === 'monthly') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } elseif ($filter === 'yearly') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
        } else {
            $days = $request->input('days', 7);
            $startDate = Carbon::now()->subDays($days)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        }

        // 1. Daily Revenue Trend (Line Chart)
        $revenueTrend = Transaction::selectRaw('transaction_date, SUM(charge_amount) as total')
            ->whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('transaction_date')
            ->orderBy('transaction_date')
            ->get();

        $trendLabels = $revenueTrend->pluck('transaction_date')->map(fn($date) => Carbon::parse($date)->format('M d'));
        $trendData = $revenueTrend->pluck('total');

        // 2. Payment Method Breakdown (Donut Chart)
        $paymentMethods = Transaction::selectRaw('payment_method, SUM(credit_amount) as total')
            ->whereIn('payment_method', ['CASH', 'CREDIT_CARD'])
            ->whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('payment_method')
            ->get();

        $paymentLabels = $paymentMethods->pluck('payment_method')->map(fn($method) => str_replace('_', ' ', $method));
        $paymentData = $paymentMethods->pluck('total');

        // 3. Department Breakdown (Donut Chart)
        $departmentRevenue = Transaction::selectRaw('department, SUM(charge_amount) as total')
            ->whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('department')
            ->get();

        $deptLabels = $departmentRevenue->pluck('department')->map(fn($dept) => str_replace('_', ' ', $dept));
        $deptData = $departmentRevenue->pluck('total');

        return response()->json([
            'trend' => [
                'labels' => $trendLabels,
                'data' => $trendData,
            ],
            'payment' => [
                'labels' => $paymentLabels,
                'data' => $paymentData,
            ],
            'department' => [
                'labels' => $deptLabels,
                'data' => $deptData,
            ],
        ]);
    }
}
