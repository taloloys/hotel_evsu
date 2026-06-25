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
                <a href="{{ route('coffeeshop.pos') }}" class="btn btn-sm btn-primary">Open POS</a>
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
            <div class="card-header bg-white fw-semibold">Recent Orders</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td><a href="{{ route('coffeeshop.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                            <td>{{ $order->customer_name }}</td>
                            <td>₱{{ number_format($order->total, 2) }}</td>
                            <td><span class="badge bg-secondary">{{ strtoupper($order->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted">No orders yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold text-danger">Needs Restocking</div>
            <div class="list-group list-group-flush">
                @forelse($lowStockProducts as $product)
                <div class="list-group-item d-flex justify-content-between">
                    <div>
                        <div class="fw-semibold">{{ $product->name }}</div>
                        <small class="text-muted">{{ $product->category?->name }}</small>
                    </div>
                    <span class="badge bg-danger">{{ $product->stock_quantity }}</span>
                </div>
                @empty
                <div class="list-group-item text-muted">All stock levels are healthy.</div>
                @endforelse
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Open Tabs</div>
            <div class="list-group list-group-flush">
                @forelse($openTabs as $tab)
                <a href="{{ route('coffeeshop.pos') }}" class="list-group-item list-group-item-action d-flex justify-content-between">
                    <span>{{ $tab->tab_name }}</span>
                    <span class="fw-bold">₱{{ number_format($tab->total, 2) }}</span>
                </a>
                @empty
                <div class="list-group-item text-muted">No open tabs.</div>
                @endforelse
            </div>
        </div>

        @if($topToday->isNotEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Top Sellers Today</div>
            <div class="list-group list-group-flush">
                @foreach($topToday as $name => $qty)
                <div class="list-group-item d-flex justify-content-between">
                    <span>{{ $name }}</span>
                    <span class="badge bg-primary">{{ $qty }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
