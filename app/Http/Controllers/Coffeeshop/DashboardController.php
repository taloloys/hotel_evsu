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

        $allProducts = PosProduct::all();

        $stats['out_stock_count'] = $allProducts->where('stock_quantity', 0)->count();

        $stats['critical_stock_count'] = $allProducts->filter(fn ($p) => $p->stock_quantity > 0 && $p->stock_quantity <= (int) ($p->effectiveLowStockThreshold() * 0.4))->count();

        $stats['low_stock_count'] = $allProducts->filter(fn ($p) => $p->stock_quantity > 0 && $p->stock_quantity <= $p->effectiveLowStockThreshold())->count();

        $stats['healthy_stock_count'] = $allProducts->filter(fn ($p) => $p->stock_quantity > $p->effectiveLowStockThreshold())->count();

        $lowStockProducts = PosProduct::with('category')
            ->lowStock()
            ->where('stock_quantity', '>', 0)
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
