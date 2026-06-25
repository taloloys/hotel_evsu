<?php

namespace App\Http\Controllers\Coffeeshop;

use App\Http\Controllers\Controller;
use App\Services\Coffeeshop\PosAnalyticsService;
use App\Services\Coffeeshop\PosInventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(Request $request, PosAnalyticsService $analytics, PosInventoryService $inventory): View
    {
        $from = $request->filled('date_from') ? Carbon::parse($request->date_from) : Carbon::today()->subDays(29);
        $to = $request->filled('date_to') ? Carbon::parse($request->date_to) : Carbon::today();

        return view('coffeeshop.statistics.index', [
            'stats' => $analytics->dashboardStats(),
            'mostSold' => $analytics->mostSoldProducts(10, $from, $to),
            'leastSold' => $analytics->leastSoldProducts(10, $from, $to),
            'topRevenue' => $analytics->topRevenueProducts(10, $from, $to),
            'dailySales' => $analytics->salesByPeriod('daily', 14),
            'weeklySales' => $analytics->salesByPeriod('weekly', 56),
            'monthlySales' => $analytics->salesByPeriod('monthly', 365),
            'suggestedRestock' => $inventory->lowStockProducts()->take(10),
            'suggestedPromote' => $analytics->suggestedPromote(10),
            'filters' => [
                'date_from' => $from->toDateString(),
                'date_to' => $to->toDateString(),
            ],
        ]);
    }
}
