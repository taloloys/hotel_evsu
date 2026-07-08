@extends('layouts.app')

@section('title', 'Inventory')
@section('pageTitle', 'Inventory Management')
@section('pageSubtitle', 'Track stock levels, restock items, and monitor alerts')

@section('content')
@include('coffeeshop.partials.alerts')

@php
    function stockStatus($product) {
        $qty = $product->stock_quantity;
        $threshold = $product->effectiveLowStockThreshold();
        if ($qty == 0) return ['Out of Stock', 'danger'];
        if ($qty <= $threshold) return ['Low Stock', 'danger'];
        if ($qty <= (int)($threshold * 1.4)) return ['Semi Low', 'warning'];
        if ($qty <= (int)($threshold * 2)) return ['Well Stocked', 'success'];
        return ['Over Stocked', 'primary'];
    }
@endphp

<div class="coffeeshop-page-shell">
    <div class="coffeeshop-hero">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
            <div>
                <div class="fw-bold fs-5">Inventory pulse at a glance</div>
                <div class="opacity-75 mt-1">Keep shelves healthy, react to low stock, and restock with less effort.</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-md-4"><div class="coffeeshop-card p-4 h-100"><div class="text-muted small">Total Products</div><div class="fs-3 fw-bold text-brown">{{ $products->total() }}</div></div></div>
        <div class="col-md-4"><div class="coffeeshop-card p-4 h-100"><div class="text-muted small">Low Stock</div><div class="fs-3 fw-bold text-danger">{{ $lowStockProducts->count() }}</div></div></div>
        <div class="col-md-4"><div class="coffeeshop-card p-4 h-100"><div class="text-muted small">Out of Stock</div><div class="fs-3 fw-bold text-dark">{{ $outOfStockCount }}</div></div></div>
    </div>

    @if($lowStockProducts->count() > 0)
    <div class="alert alert-danger rounded-4 border-0 d-flex align-items-center">
        <i class="fa-solid fa-bell me-2"></i>
        <strong>Low Stock Alert:</strong>
        <span class="ms-2">{{ $lowStockProducts->pluck('name')->take(5)->join(', ') }}</span>
    </div>
    @endif

    <div class="coffeeshop-panel p-2 p-lg-4">
        <form method="GET" class="row g-2 align-items-end mb-3">
            <div class="col-lg-5"></div>
            <div class="col-lg-4">
                <label class="form-label small text-muted mb-1">Search</label>
                <div class="input-group coffeeshop-form-control" style="border: 1px solid black; border-radius: 4px;">
                    <span class="input-group-text bg-white border-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-0" placeholder="Search products..." onkeydown="if(event.key==='Enter'){ event.preventDefault(); if(this.form.requestSubmit){ this.form.requestSubmit(); }else{ this.form.submit(); } }">
                </div>
            </div>
            <div class="col-lg-3">
                <label class="form-label small text-muted mb-1">Filter</label>
                <select name="filter" class="form-select coffeeshop-form-control" onchange="this.form.requestSubmit ? this.form.requestSubmit() : this.form.submit()" style="border: 1px solid black;">
                    <option value="">All Inventory</option>
                    <option value="out_of_stock" @selected(request('filter')=='out_of_stock')>Out of Stock</option>
                    <option value="critical_stock" @selected(request('filter')=='critical_stock')>Low Stock (≤ Threshold)</option>
                    <option value="low_stock" @selected(request('filter')=='low_stock')>Semi Low</option>
                    <option value="healthy_stock" @selected(request('filter')=='healthy_stock')>Well Stocked</option>
                    <option value="well_stocked" @selected(request('filter')=='well_stocked')>Over Stocked</option>
                </select>
            </div>
        </form>

        <div class="card border-1 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0 coffeeshop-table">
                    <thead>
                        <tr><th>Product</th><th>Category</th><th>Stock</th><th>Status</th><th>Adjust</th></tr>
                    </thead>
                    <tbody>
                    @foreach($products as $product)
                        @php [$label, $color] = stockStatus($product); @endphp
                        <tr class="{{ $product->stock_quantity == 0 ? 'table-danger' : '' }}">
                            <td class="fw-semibold text-brown">{{ $product->name }}</td>
                            <td>{{ $product->category?->name ?? '—' }}</td>
                            <td>{{ $product->stock_quantity }}</td>
                            <td><span class="coffeeshop-pill bg-{{ $color }}-subtle text-{{ $color }}">{{ $label }}</span></td>
                            <td>
                                <form action="{{ route('coffeeshop.inventory.adjust', $product) }}" method="POST" class="d-flex gap-2 flex-wrap">
                                    @csrf
                                    <select name="adjustment_type" class="form-select form-select-sm rounded-pill" style="width:160px; border: 1px solid black;">
                                        <option value="restock">Restock</option>
                                        <option value="adjustment">Adjust</option>
                                    </select>
                                    <input type="number" name="quantity" class="form-control form-control-sm rounded-pill" style="width:120px; border: 1px solid black;" required>
                                    <input type="text" name="notes" class="form-control form-control-sm rounded-pill" placeholder="Notes" style="width:330px; border: 1px solid black;">
                                    <button class="btn btn-sm btn-primary rounded-pill px-3">Apply</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if($products->hasPages())
                <div class="card-footer bg-white border-0">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</div>

@endsection