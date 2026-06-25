@extends('layouts.app')

@section('title', 'Inventory')
@section('pageTitle', 'Inventory Management')
@section('pageSubtitle', 'Track stock levels, restock items, and monitor alerts')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Default Low Stock Threshold</div><h4>{{ $defaultThreshold }}</h4></div></div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Items Needing Restock</div><h4 class="text-danger">{{ $lowStockProducts->count() }}</h4></div></div>
    </div>
</div>

@if($lowStockProducts->isNotEmpty())
<div class="alert alert-danger">
    <strong>Low stock alert:</strong>
    {{ $lowStockProducts->pluck('name')->join(', ') }}
</div>
@endif

<div class="d-flex justify-content-between mb-3">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search inventory">
        <select name="filter" class="form-select">
            <option value="">All Items</option>
            <option value="low_stock" @selected(request('filter') === 'low_stock')>Low Stock Only</option>
        </select>
        <button class="btn btn-outline-secondary">Filter</button>
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Product</th><th>Category</th><th>Stock</th><th>Threshold</th><th>Adjust</th></tr></thead>
            <tbody>
            @foreach($products as $product)
                <tr class="{{ $product->isLowStock() ? 'table-danger' : '' }}">
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category?->name }}</td>
                    <td>{{ $product->stock_quantity }}</td>
                    <td>{{ $product->effectiveLowStockThreshold() }}</td>
                    <td>
                        <form action="{{ route('coffeeshop.inventory.adjust', $product) }}" method="POST" class="d-flex gap-2">
                            @csrf
                            <select name="adjustment_type" class="form-select form-select-sm" style="width:130px;">
                                <option value="restock">Restock</option>
                                <option value="adjustment">Adjust</option>
                            </select>
                            <input type="number" name="quantity" class="form-control form-control-sm" style="width:90px;" placeholder="+/-" required>
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="Notes">
                            <button class="btn btn-sm btn-primary">Apply</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @if($products->hasPages())<div class="card-footer">{{ $products->links() }}</div>@endif
</div>
@endsection
