@extends('layouts.app')

@section('title', 'Coffee Shop Dashboard')
@section('pageTitle', 'Coffee Shop Dashboard')
@section('pageSubtitle', 'Operations overview, alerts, and quick insights')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="row g-3 mb-4">

    {{-- CARD 1 --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-muted small">Today's Sales</div>
                <h3 class="fw-bold mb-0">₱{{ number_format($stats['today_sales'], 2) }}</h3>
            </div>
        </div>
    </div>

    {{-- CARD 2 --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-muted small">Today's Orders</div>
                <h3 class="fw-bold mb-0">{{ $stats['today_orders'] }}</h3>
            </div>
        </div>
    </div>

    {{-- CARD 3 --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-muted small">Open Tabs</div>
                <h3 class="fw-bold mb-0">{{ $stats['open_tabs'] }}</h3>
            </div>
        </div>
    </div>

    {{-- CARD 4 (LOW STOCK ONLY) --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center text-center">

                <div class="text-muted small mb-2">Low Stock</div>

                <h2 class="fw-bold text-danger mb-0">
                    {{ $stats['low_stock_count'] ?? 0 }}
                </h2>

            </div>
        </div>
    </div>

</div>

<div class="row g-3">

    <div class="col-12">

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            {{-- HEADER --}}
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Dashboard Overview</span>
                <small class="text-muted">Products • Orders • Insights</small>
            </div>

            {{-- NAV PILLS --}}
            <div class="bg-light px-3 pt-3">

                <ul class="nav nav-pills nav-fill gap-2" id="dashboardTabs" role="tablist">

                    <li class="nav-item">
                        <button class="nav-link active rounded-pill shadow-sm"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-products">
                            Products
                        </button>
                    </li>

                    <li class="nav-item">
                        <button class="nav-link rounded-pill shadow-sm"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-orders">
                            Recent Orders
                        </button>
                    </li>

                    <li class="nav-item">
                        <button class="nav-link rounded-pill shadow-sm"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-overview">
                            Overview
                        </button>
                    </li>

                </ul>

            </div>

            {{-- BODY --}}
            <div class="card-body bg-white">

                <div class="tab-content">

                    {{-- ===================== PRODUCTS ===================== --}}
                    <div class="tab-pane fade show active" id="tab-products">

                        <div class="row g-3">

                            @forelse($featuredProducts as $product)

                                @php
                                    $stock = $product->stock_quantity;

                                    $isOut = $stock <= 0;
                                    $isCritical = $stock > 0 && $stock <= 20;
                                    $isLow = $stock > 20 && $stock <= 50;

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

                                <div class="col-md-3 col-6">

                                    <div class="border-0 shadow-sm rounded-4 p-3 text-center h-100 bg-light">

                                        @if($product->image_url)
                                            <img src="{{ $product->image_url }}"
                                                 class="img-fluid rounded mb-2"
                                                 style="max-height:60px;">
                                        @else
                                            <i class="fa-solid fa-mug-hot fa-2x text-warning mb-2"></i>
                                        @endif

                                        <div class="fw-semibold small">
                                            {{ $product->name }}
                                        </div>

                                        <small class="text-muted d-block">
                                            {{ Str::limit($product->description, 30) }}
                                        </small>

                                        <div class="fw-bold text-primary mt-1">
                                            ₱{{ number_format($product->price, 2) }}
                                        </div>

                                        <div class="mt-2">
                                            <span class="badge {{ $stockClass }} px-3 py-1">
                                                Stock: {{ $stock }} • {{ $stockText }}
                                            </span>
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

                    {{-- ===================== ORDERS ===================== --}}
                    <div class="tab-pane fade" id="tab-orders">

                        <div class="table-responsive">

                            <table class="table align-middle mb-0">

                                <thead class="table-light">
                                    <tr class="text-muted small">
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
                                            <a href="{{ route('coffeeshop.orders.show', $order) }}"
                                               class="fw-semibold text-decoration-none">
                                                {{ $order->order_number }}
                                            </a>
                                        </td>

                                        <td class="fw-semibold">
                                            {{ $order->customer_name }}
                                        </td>

                                        <td class="fw-bold text-primary">
                                            ₱{{ number_format($order->total, 2) }}
                                        </td>

                                        <td class="text-end">
                                            <span class="badge {{ $badge }}">
                                                {{ strtoupper($order->status) }}
                                            </span>
                                        </td>

                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            No recent orders
                                        </td>
                                    </tr>
                                @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                    {{-- ===================== OVERVIEW (CLEAN BIG UI) ===================== --}}
                    <div class="tab-pane fade" id="tab-overview">

                        <div class="row g-3">

                            {{-- LOW STOCK --}}
                            <div class="col-md-4">

                                <div class="p-4 bg-light rounded-4 shadow-sm h-100">

                                    <div class="fw-bold text-danger fs-5 mb-3">
                                        Low Stock
                                    </div>

                                    @forelse($lowStockProducts->take(4) as $product)
                                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">

                                            <span class="fw-semibold">
                                                {{ $product->name }}
                                            </span>

                                            <span class="text-danger fw-bold fs-6">
                                                {{ $product->stock_quantity }}
                                            </span>

                                        </div>
                                    @empty
                                        <div class="text-muted">No low stock</div>
                                    @endforelse

                                </div>

                            </div>

                            {{-- ACTIVE TABS --}}
                            <div class="col-md-4">

                                <div class="p-4 bg-light rounded-4 shadow-sm h-100">

                                    <div class="fw-bold text-primary fs-5 mb-3">
                                        Active Tabs
                                    </div>

                                    @forelse($openTabs->take(4) as $tab)
                                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">

                                            <span class="fw-semibold">
                                                {{ $tab->tab_name }}
                                            </span>

                                            <span class="text-primary fw-bold fs-6">
                                                ₱{{ number_format($tab->total, 2) }}
                                            </span>

                                        </div>
                                    @empty
                                        <div class="text-muted">No open tabs</div>
                                    @endforelse

                                </div>

                            </div>

                            {{-- TOP SELLERS --}}
                            <div class="col-md-4">

                                <div class="p-4 bg-light rounded-4 shadow-sm h-100">

                                    <div class="fw-bold text-warning fs-5 mb-3">
                                        Top Sellers
                                    </div>

                                    @forelse($topToday->take(4) as $name => $qty)
                                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">

                                            <span class="fw-semibold">
                                                {{ $name }}
                                            </span>

                                            <span class="badge bg-warning text-dark fs-6 px-3">
                                                {{ $qty }}
                                            </span>

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

</div>
@endsection
