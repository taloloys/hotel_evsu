@extends('layouts.app')

@section('title', 'Coffee Shop Dashboard')
@section('pageTitle', 'Coffee Shop Dashboard')
@section('pageSubtitle', 'Operations overview, alerts, and quick insights')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Today's Sales</div>
                <h3 class="fw-bold mb-0">₱{{ number_format($stats['today_sales'], 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Today's Orders</div>
                <h3 class="fw-bold mb-0">{{ $stats['today_orders'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Open Tabs</div>
                <h3 class="fw-bold mb-0">{{ $stats['open_tabs'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Low Stock Items</div>
                <h3 class="fw-bold mb-0 text-danger">{{ $stats['low_stock_count'] }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between">
                <span>Products</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @forelse($featuredProducts as $product)
                    <div class="col-md-3 col-6">
                        <div class="border rounded p-3 text-center h-100 {{ $product->isLowStock() ? 'border-danger' : '' }}">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="img-fluid rounded mb-2" style="max-height:60px;">
                            @else
                                <i class="fa-solid fa-mug-hot fa-2x text-warning mb-2"></i>
                            @endif
                            <div class="fw-semibold small">{{ $product->name }}</div>
                            <small class="text-muted d-block">{{ Str::limit($product->description, 30) }}</small>
                            <div class="fw-bold text-primary mt-1">₱{{ number_format($product->price, 2) }}</div>
                            <small class="{{ $product->isLowStock() ? 'text-danger' : 'text-muted' }}">Stock: {{ $product->stock_quantity }}</small>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-muted">No products yet. <a href="{{ route('coffeeshop.products.create') }}">Add products</a>.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">

            {{-- HEADER --}}
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Recent Orders</span>
                <small class="text-muted">Latest transactions</small>
            </div>

            {{-- TABLE --}}
            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    {{-- HEADER --}}
                    <thead class="table-light">
                        <tr class="text-muted small">
                            <th class="ps-3">Order</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th class="pe-3 text-end">Status</th>
                        </tr>
                    </thead>

                    {{-- BODY --}}
                    <tbody>

                    @forelse($recentOrders as $order)
                        <tr class="border-top">

                            {{-- ORDER --}}
                            <td class="ps-3">
                                <a href="{{ route('coffeeshop.orders.show', $order) }}"
                                class="fw-semibold text-decoration-none">
                                    {{ $order->order_number }}
                                </a>
                            </td>

                            {{-- CUSTOMER (kept, but cleaner) --}}
                            <td>
                                <div class="fw-semibold">{{ $order->customer_name }}</div>
                            </td>

                            {{-- TOTAL --}}
                            <td class="fw-bold text-primary">
                                ₱{{ number_format($order->total, 2) }}
                            </td>

                            {{-- STATUS --}}
                            <td class="pe-3 text-end">
                                @php
                                    $status = strtolower($order->status);
                                    $badge = match($status) {
                                        'paid' => 'bg-success',
                                        'pending' => 'bg-warning text-dark',
                                        'cancelled' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp

                                <span class="badge {{ $badge }}">
                                    {{ strtoupper($order->status) }}
                                </span>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fa-solid fa-receipt d-block mb-2"></i>
                                No recent orders
                            </td>
                        </tr>
                    @endforelse

                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="col-lg-4">

        {{-- 🔴 LOW STOCK --}}
        <div class="card border-0 shadow-sm mb-3">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold text-danger">Needs Restocking</span>
                <i class="fa-solid fa-triangle-exclamation text-danger"></i>
            </div>

            <div class="card-body p-0">

                @forelse($lowStockProducts as $product)
                    <div class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom">

                        <div>
                            <div class="fw-semibold">{{ $product->name }}</div>
                            <small class="text-muted">{{ $product->category?->name }}</small>
                        </div>

                        <span class="badge bg-danger rounded-pill px-3">
                            {{ $product->stock_quantity }}
                        </span>

                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        All stock levels are healthy
                    </div>
                @endforelse

            </div>
        </div>


        {{-- 🟦 OPEN TABS --}}
        <div class="card border-0 shadow-sm mb-3">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Open Tabs</span>
                <i class="fa-solid fa-folder-open text-primary"></i>
            </div>

            <div class="card-body p-0">

                @forelse($openTabs as $tab)
                    <a href="{{ route('coffeeshop.pos') }}"
                    class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom text-decoration-none">

                        <span class="fw-semibold text-dark">
                            {{ $tab->tab_name }}
                        </span>

                        <span class="fw-bold text-primary">
                            ₱{{ number_format($tab->total, 2) }}
                        </span>

                    </a>
                @empty
                    <div class="text-center text-muted py-4">
                        No open tabs
                    </div>
                @endforelse

            </div>
        </div>


        {{-- ⭐ TOP SELLERS --}}
        @if($topToday->isNotEmpty())
        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Top Sellers Today</span>
                <i class="fa-solid fa-star text-warning"></i>
            </div>

            <div class="card-body p-0">

                @foreach($topToday as $name => $qty)
                    <div class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom">

                        <span class="text-dark fw-semibold">
                            {{ $name }}
                        </span>

                        <span class="badge bg-primary rounded-pill px-3">
                            {{ $qty }}
                        </span>

                    </div>
                @endforeach

            </div>
        </div>
        @endif

    </div>
</div>
@endsection
