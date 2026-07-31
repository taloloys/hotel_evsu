<?php

namespace App\Services\Coffeeshop;

use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\PosProduct;
use App\Models\PosTab;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PosAnalyticsService
{
    public function dashboardStats(): array
    {
        $today = Carbon::today();

        return [
            'today_sales' => (float) PosOrder::closed()->whereDate('closed_at', $today)->sum('total'),
            'today_orders' => PosOrder::closed()->whereDate('closed_at', $today)->count(),
            'open_tabs' => PosTab::open()->count(),
            'low_stock_count' => PosProduct::lowStock()->count(),
        ];
    }

    public function mostSoldProducts(int $limit = 10, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        return $this->productSalesQuery($from, $to)
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();
    }

    public function leastSoldProducts(int $limit = 10, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        return $this->productSalesQuery($from, $to)
            ->orderBy('total_qty')
            ->limit($limit)
            ->get();
    }

    public function topRevenueProducts(int $limit = 10, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        return $this->productSalesQuery($from, $to)
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();
    }

    public function salesByPeriod(string $period, Carbon $from, Carbon $to): Collection
    {
        $orders = PosOrder::closed()
            ->whereBetween('closed_at', [$from, $to])
            ->get(['total', 'closed_at']);

        return $orders->groupBy(function ($order) use ($period) {
            $date = Carbon::parse($order->closed_at);

            return match ($period) {
                'weekly' => $date->startOfWeek()->format('Y-m-d'),
                'monthly' => $date->format('Y-m'),
                default => $date->format('Y-m-d'),
            };
        })->map(function ($items, $key) {
            return [
                'label' => $key,
                'total' => $items->sum('total'),
            ];
        })->values();
    }

    public function productTrend(int $productId, int $days = 14): Collection
    {
        $from = Carbon::today()->subDays($days - 1);

        $items = PosOrderItem::query()
            ->join('pos_orders', 'pos_order_items.order_id', '=', 'pos_orders.order_id')
            ->where('pos_order_items.product_id', $productId)
            ->where('pos_orders.status', 'closed')
            ->whereDate('pos_orders.closed_at', '>=', $from)
            ->selectRaw('DATE(pos_orders.closed_at) as sale_date, SUM(pos_order_items.quantity) as qty')
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get();

        return $items->map(fn ($row) => [
            'label' => $row->sale_date,
            'quantity' => (int) $row->qty,
        ]);
    }

    public function suggestedRestock(int $limit = 10): Collection
    {
        return PosProduct::with('category')
            ->lowStock()
            ->get()
            ->sortByDesc(function (PosProduct $product) {
                $recentSales = PosOrderItem::query()
                    ->join('pos_orders', 'pos_order_items.order_id', '=', 'pos_orders.order_id')
                    ->where('pos_order_items.product_id', $product->product_id)
                    ->where('pos_orders.status', 'closed')
                    ->where('pos_orders.closed_at', '>=', Carbon::today()->subDays(30))
                    ->sum('pos_order_items.quantity');

                return [$recentSales, -$product->stock_quantity];
            })
            ->take($limit)
            ->values();
    }

    public function suggestedPromote(int $limit = 10): Collection
    {
        $from = Carbon::today()->subDays(30);

        return PosProduct::with('category')
            ->active()
            ->where(function ($q) {
                // Always include none-tracked (made-to-order) products regardless of stock.
                // For manual-tracked products, only include those with stock > 0.
                $q->where('stock_tracking', '!=', 'manual')
                    ->orWhere('stock_quantity', '>', 0);
            })
            ->get()
            ->map(function (PosProduct $product) use ($from) {
                $sales = PosOrderItem::query()
                    ->join('pos_orders', 'pos_order_items.order_id', '=', 'pos_orders.order_id')
                    ->where('pos_order_items.product_id', $product->product_id)
                    ->where('pos_orders.status', 'closed')
                    ->where('pos_orders.closed_at', '>=', $from)
                    ->sum('pos_order_items.quantity');

                return [
                    'product' => $product,
                    'recent_sales' => (int) $sales,
                    'stock_quantity' => $product->isManualTracked() ? $product->stock_quantity : null,
                ];
            })
            ->sortBy(function ($row) {
                return [$row['recent_sales'], -($row['stock_quantity'] ?? 0)];
            })
            ->take($limit)
            ->values();
    }

    public function frequentCustomers(int $limit = 10): Collection
    {
        return PosOrder::closed()
            ->select('customer_name', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total) as total_spent'))
            ->groupBy('customer_name')
            ->orderByDesc('order_count')
            ->limit($limit)
            ->get();
    }

    public function customerHistory(?string $search = null, ?string $status = null)
    {
        $tabsQuery = PosTab::with(['items.product', 'room', 'order'])
            ->orderByDesc('opened_at');

        if ($search) {
            $tabsQuery->where('tab_name', 'like', "%{$search}%");
        }

        if ($status === 'open') {
            $tabsQuery->where('status', 'open');
        } elseif ($status === 'closed') {
            $tabsQuery->where('status', 'closed');
        } elseif ($status === 'cancelled') {
            $tabsQuery->where('status', 'cancelled');
        }

        $ordersQuery = PosOrder::with(['items', 'folio.guest'])
            ->orderByDesc('created_at');

        if ($search) {
            $ordersQuery->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%");
            });
        }

        if ($status && in_array($status, ['closed', 'cancelled', 'refunded', 'open', 'active'], true)) {
            $ordersQuery->where('status', $status);
        }

        return [
            'tabs' => $tabsQuery->limit(50)->get(),
            'orders' => $ordersQuery->limit(50)->get(),
            'frequent' => $this->frequentCustomers(10),
        ];
    }

    private function productSalesQuery(?Carbon $from, ?Carbon $to)
    {
        $query = PosOrderItem::query()
            ->join('pos_orders', 'pos_order_items.order_id', '=', 'pos_orders.order_id')
            ->where('pos_orders.status', 'closed')
            ->selectRaw('pos_order_items.product_id, pos_order_items.product_name, SUM(pos_order_items.quantity) as total_qty, SUM(pos_order_items.line_total) as total_revenue');

        if ($from) {
            $query->whereDate('pos_orders.closed_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('pos_orders.closed_at', '<=', $to);
        }

        return $query->groupBy('pos_order_items.product_id', 'pos_order_items.product_name');
    }
}
