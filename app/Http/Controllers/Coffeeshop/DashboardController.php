<?php

namespace App\Http\Controllers\Coffeeshop;

use App\Http\Controllers\Controller;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\PosProduct;
use App\Models\PosTab;
use App\Services\Coffeeshop\PosAnalyticsService;
use App\Services\Coffeeshop\PosInventoryService;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the cafeteria dashboard.
     */
    public function index(PosAnalyticsService $analytics, PosInventoryService $inventory): View
    {
        $stats = $analytics->dashboardStats();

        // 1. Featured Product of the Month (and fallback to all-time or active products)
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $featuredItem = PosOrderItem::query()
            ->join('pos_orders', 'pos_order_items.order_id', '=', 'pos_orders.order_id')
            ->where('pos_orders.status', 'closed')
            ->whereBetween('pos_orders.closed_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('
                pos_order_items.product_id,
                pos_order_items.product_name,
                COUNT(DISTINCT pos_order_items.order_id) as number_of_orders,
                SUM(pos_order_items.quantity) as total_quantity_sold,
                SUM(pos_order_items.line_total) as total_revenue
            ')
            ->groupBy('pos_order_items.product_id', 'pos_order_items.product_name')
            ->orderByDesc('total_quantity_sold')
            ->first();

        $featuredProduct = null;
        if ($featuredItem) {
            $productModel = PosProduct::with('category')->find($featuredItem->product_id);
            $featuredProduct = [
                'name' => $featuredItem->product_name,
                'image_url' => $productModel?->image_url,
                'category_name' => $productModel?->category?->name ?? 'Uncategorized',
                'category_type' => $this->getProductCategoryType($productModel?->category?->name ?? 'Food'),
                'number_of_orders' => (int) $featuredItem->number_of_orders,
                'total_quantity_sold' => (int) $featuredItem->total_quantity_sold,
                'total_revenue' => (float) $featuredItem->total_revenue,
            ];
        } else {
            // Fallback 1: Get overall top product (all time)
            $fallbackItem = PosOrderItem::query()
                ->join('pos_orders', 'pos_order_items.order_id', '=', 'pos_orders.order_id')
                ->where('pos_orders.status', 'closed')
                ->selectRaw('
                    pos_order_items.product_id,
                    pos_order_items.product_name,
                    COUNT(DISTINCT pos_order_items.order_id) as number_of_orders,
                    SUM(pos_order_items.quantity) as total_quantity_sold,
                    SUM(pos_order_items.line_total) as total_revenue
                ')
                ->groupBy('pos_order_items.product_id', 'pos_order_items.product_name')
                ->orderByDesc('total_quantity_sold')
                ->first();

            if ($fallbackItem) {
                $productModel = PosProduct::with('category')->find($fallbackItem->product_id);
                $featuredProduct = [
                    'name' => $fallbackItem->product_name,
                    'image_url' => $productModel?->image_url,
                    'category_name' => $productModel?->category?->name ?? 'Uncategorized',
                    'category_type' => $this->getProductCategoryType($productModel?->category?->name ?? 'Food'),
                    'number_of_orders' => (int) $fallbackItem->number_of_orders,
                    'total_quantity_sold' => (int) $fallbackItem->total_quantity_sold,
                    'total_revenue' => (float) $fallbackItem->total_revenue,
                ];
            } else {
                // Fallback 2: Get first active product with 0 sales
                $firstProduct = PosProduct::with('category')->active()->first();
                if ($firstProduct) {
                    $featuredProduct = [
                        'name' => $firstProduct->name,
                        'image_url' => $firstProduct->image_url,
                        'category_name' => $firstProduct->category?->name ?? 'Uncategorized',
                        'category_type' => $this->getProductCategoryType($firstProduct->category?->name ?? 'Food'),
                        'number_of_orders' => 0,
                        'total_quantity_sold' => 0,
                        'total_revenue' => 0.0,
                    ];
                }
            }
        }

        // 2. Best-Selling Products of the Month (and fallback to all-time or active products)
        $bestSellers = PosOrderItem::query()
            ->join('pos_orders', 'pos_order_items.order_id', '=', 'pos_orders.order_id')
            ->where('pos_orders.status', 'closed')
            ->whereBetween('pos_orders.closed_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('
                pos_order_items.product_id,
                pos_order_items.product_name,
                SUM(pos_order_items.quantity) as total_qty,
                SUM(pos_order_items.line_total) as total_revenue
            ')
            ->groupBy('pos_order_items.product_id', 'pos_order_items.product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        if ($bestSellers->isEmpty()) {
            // Fallback 1: Overall top products
            $bestSellers = PosOrderItem::query()
                ->join('pos_orders', 'pos_order_items.order_id', '=', 'pos_orders.order_id')
                ->where('pos_orders.status', 'closed')
                ->selectRaw('
                    pos_order_items.product_id,
                    pos_order_items.product_name,
                    SUM(pos_order_items.quantity) as total_qty,
                    SUM(pos_order_items.line_total) as total_revenue
                ')
                ->groupBy('pos_order_items.product_id', 'pos_order_items.product_name')
                ->orderByDesc('total_qty')
                ->limit(5)
                ->get();
        }

        $bestSellersData = $bestSellers->map(function ($item) {
            $productModel = PosProduct::with('category')->find($item->product_id);

            return [
                'name' => $item->product_name,
                'category_name' => $productModel?->category?->name ?? 'Uncategorized',
                'category_type' => $this->getProductCategoryType($productModel?->category?->name ?? 'Food'),
                'total_qty' => (int) $item->total_qty,
                'total_revenue' => (float) $item->total_revenue,
            ];
        });

        if ($bestSellersData->isEmpty()) {
            // Fallback 2: Get active products
            $bestSellersData = PosProduct::with('category')->active()->limit(5)->get()->map(function ($product) {
                return [
                    'name' => $product->name,
                    'category_name' => $product->category?->name ?? 'Uncategorized',
                    'category_type' => $this->getProductCategoryType($product->category?->name ?? 'Food'),
                    'total_qty' => 0,
                    'total_revenue' => 0.0,
                ];
            });
        }

        // 3. Sales Overview stats
        $salesOverview = [
            'today_revenue' => (float) PosOrder::closed()->whereDate('closed_at', Carbon::today())->sum('total'),
            'weekly_revenue' => (float) PosOrder::closed()->whereBetween('closed_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total'),
            'monthly_revenue' => (float) PosOrder::closed()->whereBetween('closed_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('total'),
            'today_orders' => PosOrder::whereDate('created_at', Carbon::today())->count(),
            'pending_orders' => PosOrder::whereIn('status', ['open', 'active'])->count(),
            'completed_today' => PosOrder::closed()->whereDate('closed_at', Carbon::today())->count(),
            'completed_overall' => PosOrder::closed()->count(),
            'cancelled_today' => PosOrder::whereIn('status', ['cancelled', 'refunded'])->whereDate('created_at', Carbon::today())->count(),
            'cancelled_overall' => PosOrder::whereIn('status', ['cancelled', 'refunded'])->count(),
        ];

        // 4. Inventory Overview stats
        $allActiveProducts = PosProduct::active()->get();
        $inventoryOverview = [
            'low_stock' => $allActiveProducts->filter(fn ($p) => $p->stock_quantity > 0 && $p->isLowStock())->count(),
            'out_of_stock' => $allActiveProducts->filter(fn ($p) => $p->is_stockable && $p->stock_quantity <= 0)->count(),
            'total_available' => $allActiveProducts->filter(fn ($p) => ! $p->is_stockable || $p->stock_quantity > 0)->count(),
            'needs_restocking' => $allActiveProducts->filter(fn ($p) => $p->isLowStock() || ($p->is_stockable && $p->stock_quantity <= 0))->count(),
        ];

        // 5. Category Distribution for animations
        $categorySales = PosOrderItem::query()
            ->join('pos_orders', 'pos_order_items.order_id', '=', 'pos_orders.order_id')
            ->join('pos_products', 'pos_order_items.product_id', '=', 'pos_products.product_id')
            ->join('pos_categories', 'pos_products.category_id', '=', 'pos_categories.category_id')
            ->where('pos_orders.status', 'closed')
            ->whereBetween('pos_orders.closed_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('pos_categories.name as category_name, SUM(pos_order_items.quantity) as qty')
            ->groupBy('pos_categories.name')
            ->orderByDesc('qty')
            ->get();

        $totalCategoryQty = $categorySales->sum('qty');
        $categoryDistribution = $categorySales->map(function ($cat) use ($totalCategoryQty) {
            return [
                'name' => $cat->category_name,
                'qty' => $cat->qty,
                'percentage' => $totalCategoryQty > 0 ? round(($cat->qty / $totalCategoryQty) * 100) : 0,
            ];
        });

        // Fallback for visual completeness
        if ($categoryDistribution->isEmpty()) {
            $categoryDistribution = collect([
                ['name' => 'Coffee', 'percentage' => 45, 'qty' => 0],
                ['name' => 'Tea', 'percentage' => 15, 'qty' => 0],
                ['name' => 'Beer', 'percentage' => 10, 'qty' => 0],
                ['name' => 'Food', 'percentage' => 20, 'qty' => 0],
                ['name' => 'Dessert', 'percentage' => 10, 'qty' => 0],
            ]);
        }

        $allProducts = PosProduct::all();

        $stats['out_stock_count'] = $allProducts->filter(fn ($p) => $p->is_stockable && $p->stock_quantity <= 0)->count();
        $stats['critical_stock_count'] = $allProducts->filter(fn ($p) => $p->is_stockable && $p->stock_quantity > 0 && $p->stock_quantity <= (int) ($p->effectiveLowStockThreshold() * 0.4))->count();
        $stats['low_stock_count'] = $allProducts->filter(fn ($p) => $p->is_stockable && $p->stock_quantity > 0 && $p->stock_quantity <= $p->effectiveLowStockThreshold())->count();
        $stats['healthy_stock_count'] = $allProducts->filter(fn ($p) => $p->is_stockable && $p->stock_quantity > $p->effectiveLowStockThreshold())->count();

        $lowStockProducts = PosProduct::with('category')
            ->lowStock()
            ->where('stock_quantity', '>', 0)
            ->orderBy('stock_quantity', 'asc')
            ->limit(8)
            ->get();

        $recentOrders = PosOrder::with(['items'])->orderByDesc('created_at')->limit(10)->get();

        $topToday = PosOrder::closed()
            ->whereDate('closed_at', Carbon::today())
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
            'featuredProducts',
            'featuredProduct',
            'bestSellersData',
            'salesOverview',
            'inventoryOverview',
            'categoryDistribution'
        ));
    }

    /**
     * Get the recent orders HTML partial view.
     */
    public function recentOrdersPartial(): View
    {
        $recentOrders = PosOrder::with(['items'])->orderByDesc('created_at')->limit(10)->get();

        return view('coffeeshop.dashboard.partials.recent_orders', compact('recentOrders'));
    }

    /**
     * Helper to classify a category name into "Food" or "Beverage".
     */
    private function getProductCategoryType(string $categoryName): string
    {
        $beverageKeywords = ['coffee', 'tea', 'beer', 'beverage', 'beverages', 'drink', 'drinks', 'juice', 'soda', 'wine', 'milk', 'latte', 'cappuccino'];
        $normalized = strtolower($categoryName);
        foreach ($beverageKeywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return 'Beverage';
            }
        }

        return 'Food';
    }
}
