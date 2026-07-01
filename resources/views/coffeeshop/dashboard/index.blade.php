@extends('layouts.app')

@section('title', 'Coffee Shop Dashboard')
@section('pageTitle', 'Coffee Shop Dashboard')
@section('pageSubtitle', 'Operations overview, alerts, and quick insights')

@section('content')
@include('coffeeshop.partials.alerts')

<style>
:root {
    --coffee-950: #2f1c16;
    --coffee-800: #4e342e;
    --coffee-700: #6d4c41;
    --cream: #f8f5f2;
    --latte: #efe1cf;
    --caramel: #a97142;
    --accent-green: #4caf50;
    --accent-red: #e53935;
    --border-soft: #e7dccf;
    --shadow-soft: 0 14px 34px rgba(78, 52, 46, 0.08);
}

body {
    font-family: 'Inter', 'Segoe UI', sans-serif;
}

.dashboard-shell {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.dashboard-hero {
    background: linear-gradient(135deg, var(--coffee-800) 0%, #6a4338 100%);
    color: white;
    border-radius: 1.25rem;
    padding: 1.5rem 1.5rem 1.4rem;
    box-shadow: var(--shadow-soft);
    overflow: hidden;
    position: relative;
}

.dashboard-hero::after {
    content: '';
    position: absolute;
    inset: auto -30px -60px auto;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
}

.dashboard-hero .hero-title {
    font-size: 1.35rem;
    font-weight: 700;
}

.dashboard-hero .hero-subtitle {
    color: rgba(255,255,255,0.78);
    font-size: 0.95rem;
}

.dashboard-hero .hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.stat-card {
    border: 1px solid var(--border-soft);
    border-radius: 1rem;
    background: linear-gradient(180deg, #fffdfb 0%, #fcf7f0 100%);
    box-shadow: var(--shadow-soft);
    transition: transform 180ms ease, box-shadow 180ms ease;
    min-height: 132px;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 36px rgba(78, 52, 46, 0.12);
}

.stat-card .icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: white;
    margin-bottom: 0.85rem;
}

.stat-card .stat-label {
    color: #7f6a5a;
    font-size: 0.8rem;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    font-weight: 700;
}

.stat-card .stat-value {
    font-size: 1.45rem;
    font-weight: 700;
    color: var(--coffee-800);
}

.stat-card.sales .icon-wrap { background: linear-gradient(135deg, var(--caramel), #c4844f); }
.stat-card.orders .icon-wrap { background: linear-gradient(135deg, var(--coffee-700), #8d5a40); }
.stat-card.tabs .icon-wrap { background: linear-gradient(135deg, #5d8b53, var(--accent-green)); }
.stat-card.low-stock .icon-wrap { background: linear-gradient(135deg, var(--accent-red), #ff6f61); }

.dashboard-panel {
    background: #fffdfb;
    border: 1px solid var(--border-soft);
    border-radius: 1.15rem;
    box-shadow: var(--shadow-soft);
    overflow: hidden;
}

.dashboard-panel .panel-header {
    background: linear-gradient(90deg, #fff8ef 0%, #f8efe3 100%);
    border-bottom: 1px solid var(--border-soft);
    padding: 1rem 1.2rem;
}

#dashboardTabs .nav-link {
    background-color: #f4ebdf;
    color: #6b4d3b;
    border: 1px solid #e6d9c5;
    font-weight: 600;
    transition: all 180ms ease;
    border-radius: 999px;
}

#dashboardTabs .nav-link:hover {
    background-color: #efe0c5;
    color: var(--coffee-800);
}

#dashboardTabs .nav-link.active {
    background: linear-gradient(135deg, var(--coffee-800), #6b4338);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 8px 20px rgba(78, 52, 46, 0.2);
}

.dashboard-card {
    border: 1px solid var(--border-soft);
    border-radius: 1rem;
    padding: 1rem;
    background: linear-gradient(180deg, #fffdfb 0%, #fcf7f0 100%);
    box-shadow: 0 8px 22px rgba(78, 52, 46, 0.06);
    transition: transform 180ms ease, box-shadow 180ms ease;
    height: 100%;
}

.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(78, 52, 46, 0.1);
}

.dashboard-table th {
    background-color: #f6ebdc !important;
    color: #6b4d3b;
    font-size: 0.78rem;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    font-weight: 700;
}

.dashboard-table tbody tr {
    transition: background-color 150ms ease;
}

.dashboard-table tbody tr:hover {
    background-color: #fcf5ea;
}

.dashboard-table td {
    padding-top: 0.9rem;
    padding-bottom: 0.9rem;
}

.dashboard-list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.7rem 0;
    border-bottom: 1px solid #f0e4d6;
}

.dashboard-list-item:last-child {
    border-bottom: 0;
}

.dashboard-pill {
    border-radius: 999px;
    padding: 0.42rem 0.75rem;
    font-size: 0.76rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.text-brown {
    color: var(--coffee-800) !important;
}

.btn-soft {
    border-radius: 999px;
    padding: 0.6rem 1rem;
    font-weight: 600;
    transition: transform 180ms ease, box-shadow 180ms ease;
}

.btn-soft:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(78, 52, 46, 0.12);
}
</style>

<div class="dashboard-shell">
    <div class="dashboard-hero">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
            <div>
                <div class="hero-title">Freshly brewed insights for the floor</div>
                <div class="hero-subtitle mt-2">Track sales, monitor stock, and keep service moving without extra clicks.</div>
            </div>
            <div class="hero-actions">
                <a href="{{ route('coffeeshop.pos') }}" class="btn btn-light btn-soft text-dark">
                    <i class="fa-solid fa-cash-register me-2"></i>Open POS
                </a>
                <a href="{{ route('coffeeshop.orders') }}" class="btn btn-outline-light btn-soft">
                    <i class="fa-solid fa-receipt me-2"></i>View Orders
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card sales p-4 h-100">
                <div class="icon-wrap"><i class="fa-solid fa-chart-line"></i></div>
                <div class="stat-label">Today's Sales</div>
                <div class="stat-value">₱{{ number_format($stats['today_sales'], 2) }}</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card orders p-4 h-100">
                <div class="icon-wrap"><i class="fa-solid fa-receipt"></i></div>
                <div class="stat-label">Today's Orders</div>
                <div class="stat-value">{{ $stats['today_orders'] }}</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card tabs p-4 h-100">
                <div class="icon-wrap"><i class="fa-solid fa-utensils"></i></div>
                <div class="stat-label">Open Tabs</div>
                <div class="stat-value">{{ $stats['open_tabs'] }}</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card low-stock p-4 h-100">
                <div class="icon-wrap"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="stat-label">Low Stock</div>
                <div class="stat-value">{{ $stats['low_stock_count'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="dashboard-panel">
        <div class="panel-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h5 class="fw-bold mb-1">Dashboard overview</h5>
                <div class="text-muted small">Products, recent orders, and useful floor insights.</div>
            </div>
            <div class="text-muted small">Live from your café operations</div>
        </div>

        <div class="p-3 p-md-4">
            <ul class="nav nav-pills nav-fill gap-2 mb-3" id="dashboardTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-products">
                        Featured Products
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-orders">
                        Recent Orders
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-overview">
                        Quick Overview
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-products">
                    <div class="row g-3">
                        @forelse($featuredProducts as $product)
                            @php
                                $stock = $product->stock_quantity;
                                $threshold = $product->effectiveLowStockThreshold();

                                $isOut = $stock <= 0;
                                $isCritical = $stock > 0 && $stock <= (int)($threshold * 0.4);
                                $isLow = $stock > (int)($threshold * 0.4) && $stock <= $threshold;

                                $stockClass = match(true) {
                                    $isOut => 'bg-secondary',
                                    $isCritical => 'bg-danger',
                                    $isLow => 'bg-warning text-dark',
                                    default => 'bg-success'
                                };

                                $stockText = match(true) {
                                    $isOut => 'Out',
                                    $isCritical => 'Critical',
                                    $isLow => 'Low',
                                    default => 'OK'
                                };
                            @endphp

                            <div class="col-xl-3 col-md-6">
                                <div class="dashboard-card text-center">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" class="img-fluid rounded mb-3" style="max-height:70px; object-fit:cover;">
                                    @else
                                        <div class="mb-3"><i class="fa-solid fa-mug-hot fa-2x text-warning"></i></div>
                                    @endif

                                    <div class="fw-bold text-dark">{{ $product->name }}</div>
                                    <div class="small text-muted mt-1">{{ Str::limit($product->description, 35) }}</div>
                                    <div class="fw-bold text-brown mt-2">₱{{ number_format($product->price, 2) }}</div>
                                    <div class="mt-3">
                                        <span class="dashboard-pill {{ $stockClass }}">Stock: {{ $stock }} • {{ $stockText }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-muted">
                                No products yet. <a href="{{ route('coffeeshop.products.create') }}">Add products</a>.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-orders">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 dashboard-table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th class="text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    @php
                                        $status = strtolower($order->status);
                                        $badge = match($status) {
                                            'paid' => 'bg-success',
                                            'pending' => 'bg-warning text-dark',
                                            'cancelled' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp

                                    <tr>
                                        <td>
                                            <a href="{{ route('coffeeshop.orders.show', $order) }}" class="fw-semibold text-decoration-none text-dark">
                                                {{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td class="fw-semibold">{{ $order->customer_name }}</td>
                                        <td class="fw-bold text-brown">₱{{ number_format($order->total, 2) }}</td>
                                        <td class="text-end">
                                            <span class="dashboard-pill {{ $badge }}">{{ strtoupper($order->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No recent orders</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-overview">
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <div class="dashboard-card h-100">
                                <div class="fw-bold text-danger mb-3">Low Stock</div>
                                @forelse($lowStockProducts->take(4) as $product)
                                    <div class="dashboard-list-item">
                                        <span class="fw-semibold">{{ $product->name }}</span>
                                        <span class="text-danger fw-bold">{{ $product->stock_quantity }}</span>
                                    </div>
                                @empty
                                    <div class="text-muted">No low stock</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="dashboard-card h-100">
                                <div class="fw-bold text-success mb-3">Active Tabs</div>
                                @forelse($openTabs->take(4) as $tab)
                                    <div class="dashboard-list-item">
                                        <span class="fw-semibold">{{ $tab->tab_name }}</span>
                                        <span class="text-success fw-bold">₱{{ number_format($tab->total, 2) }}</span>
                                    </div>
                                @empty
                                    <div class="text-muted">No open tabs</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="dashboard-card h-100">
                                <div class="fw-bold text-warning mb-3">Top Sellers</div>
                                @forelse($topToday->take(4) as $name => $qty)
                                    <div class="dashboard-list-item">
                                        <span class="fw-semibold">{{ $name }}</span>
                                        <span class="badge bg-warning text-dark">{{ $qty }}</span>
                                    </div>
                                @empty
                                    <div class="text-muted">No sales today</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
