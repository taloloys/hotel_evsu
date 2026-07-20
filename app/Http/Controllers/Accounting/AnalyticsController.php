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

        $startDate = null;
        $endDate = Carbon::now()->endOfDay();

        if ($filter === 'today') {
            $startDate = Carbon::today()->startOfDay();
        } elseif ($filter === 'weekly') {
            $startDate = Carbon::now()->startOfWeek();
        } elseif ($filter === 'monthly') {
            $startDate = Carbon::now()->startOfMonth();
        } elseif ($filter === 'yearly') {
            $startDate = Carbon::now()->startOfYear();
        }

        // 1. Daily Revenue Trend (Line Chart)
        $trendQuery = Transaction::selectRaw('DATE(timestamp) as date_val, SUM(charge_amount) as total');
        if ($startDate) {
            $trendQuery->whereBetween('timestamp', [$startDate, $endDate]);
        }
        $revenueTrend = $trendQuery->groupBy('date_val')
            ->orderBy('date_val')
            ->get();

        $trendLabels = $revenueTrend->pluck('date_val')->map(fn ($date) => Carbon::parse($date)->format('M d'));
        $trendData = $revenueTrend->pluck('total');

        // 2. Payment Method Breakdown (Donut Chart)
        $paymentQuery = Transaction::selectRaw('payment_method, SUM(credit_amount) as total')
            ->where('credit_amount', '>', 0);
        if ($startDate) {
            $paymentQuery->whereBetween('timestamp', [$startDate, $endDate]);
        }
        $paymentMethods = $paymentQuery->groupBy('payment_method')->get();

        $paymentLabels = $paymentMethods->pluck('payment_method')->map(fn ($method) => ucwords(strtolower(str_replace('_', ' ', $method))));
        $paymentData = $paymentMethods->pluck('total');

        // 3. Department Breakdown (Donut Chart)
        $deptQuery = Transaction::selectRaw('department, SUM(charge_amount) as total')
            ->where('charge_amount', '>', 0);
        if ($startDate) {
            $deptQuery->whereBetween('timestamp', [$startDate, $endDate]);
        }
        $departmentRevenue = $deptQuery->groupBy('department')->get();

        $deptLabels = $departmentRevenue->pluck('department')->map(fn ($dept) => ucwords(strtolower(str_replace('_', ' ', $dept))));
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
