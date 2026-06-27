<?php

namespace App\Http\Controllers\Coffeeshop;

use App\Http\Controllers\Controller;
use App\Models\PosOrder;
use App\Models\PosProduct;
use App\Models\PosTab;
use App\Services\Coffeeshop\PosAnalyticsService;
use App\Services\Coffeeshop\PosInventoryService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(PosAnalyticsService $analytics, PosInventoryService $inventory): View
    {
        $stats = $analytics->dashboardStats();

        $stats['out_stock_count'] = PosProduct::where('stock_quantity', 0)->count();

        $stats['critical_stock_count'] = PosProduct::whereBetween('stock_quantity', [1, 20])->count();

        $stats['low_stock_count'] = PosProduct::whereBetween('stock_quantity', [21, 50])->count();

        $stats['healthy_stock_count'] = PosProduct::where('stock_quantity', '>', 50)->count();
    $lowStockProducts = PosProduct::with('category')
    ->where('stock_quantity', '>', 0)
    ->where('stock_quantity', '<=', 50) 
    ->orderBy('stock_quantity', 'asc')
    ->limit(8)
    ->get();
        $recentOrders = PosOrder::with('items')->orderByDesc('created_at')->limit(8)->get();
        $topToday = PosOrder::closed()
            ->whereDate('closed_at', today())
            ->with('items')
            ->get()
            ->flatMap(fn ($order) => $order->items)
            ->groupBy('product_name')
            ->map(fn ($items) => $items->sum('quantity'))
            ->sortDesc()
            ->take(5);

        $openTabs = PosTab::open()->with('items')->orderByDesc('opened_at')->limit(6)->get();
        $featuredProducts = PosProduct::with('category')->active()->orderBy('name')->limit(12)->get();

        return view('coffeeshop.dashboard.index', compact(
            'stats',
            'lowStockProducts',
            'recentOrders',
            'topToday',
            'openTabs',
            'featuredProducts'
        ));
    }
}
