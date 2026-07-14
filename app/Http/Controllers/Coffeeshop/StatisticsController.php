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
        $from = $request->filled('date_from')
            ? Carbon::parse($request->date_from)
            : Carbon::today()->subDays(29);

        $to = $request->filled('date_to')
            ? Carbon::parse($request->date_to)
            : Carbon::today();

        return view('coffeeshop.statistics.index', [

            // ✅ NOW FILTERED STATS (IMPORTANT FIX)
            'stats' => $analytics->dashboardStats($from, $to),

            // ✅ FILTERED REPORTS
            'mostSold' => $analytics->mostSoldProducts(10, $from, $to),
            'leastSold' => $analytics->leastSoldProducts(10, $from, $to),
            'topRevenue' => $analytics->topRevenueProducts(10, $from, $to),

            // ❗ FIXED: pass same date range instead of static data
            'dailySales' => $analytics->salesByPeriod('daily', $from, $to),
            'weeklySales' => $analytics->salesByPeriod('weekly', $from, $to),
            'monthlySales' => $analytics->salesByPeriod('monthly', $from, $to),

            // ⚠️ inventory still global unless service supports date range
            'suggestedRestock' => $inventory->lowStockProducts()->take(10),

            'suggestedPromote' => $analytics->suggestedPromote(10, $from, $to),

            'filters' => [
                'date_from' => $from->toDateString(),
                'date_to' => $to->toDateString(),
            ],
        ]);
    }
}
