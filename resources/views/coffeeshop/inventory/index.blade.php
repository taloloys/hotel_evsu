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

{{-- SUMMARY CARDS --}}
<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Products</div>
                    <div class="fs-3 fw-bold text-primary">
                        {{ $products->total() }}
                    </div>
                </div>
                <i class="fa-solid fa-boxes-stacked text-primary fs-3"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Low Stock</div>
                    <div class="fs-3 fw-bold text-danger">
                        {{ $lowStockProducts->count() }}
                    </div>
                </div>
                <i class="fa-solid fa-triangle-exclamation text-danger fs-3"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Out of Stock</div>
                    <div class="fs-3 fw-bold text-dark">
                        {{ $outOfStockCount }}
                    </div>
                </div>
                <i class="fa-solid fa-circle-xmark text-dark fs-3"></i>
            </div>
        </div>
    </div>

</div>

{{-- ALERT --}}
@if($lowStockProducts->count() > 0)
<div class="alert alert-danger d-flex align-items-center">
    <i class="fa-solid fa-bell me-2"></i>
    <strong>Low Stock Alert:</strong>
    <span class="ms-2">
        {{ $lowStockProducts
            ->pluck('name')
            ->take(5)
            ->join(', ') }}
    </span>
</div>
@endif

{{-- SEARCH + FILTER --}}
<div class="d-flex justify-content-end mb-3">
    <form method="GET" class="d-flex gap-2 align-items-center">

        <div class="input-group" style="width: 450px; border: 1px solid;">
            <span class="input-group-text bg-white">
                <i class="fa-solid fa-magnifying-glass text-muted"></i>
            </span>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   class="form-control"
                   placeholder="Search products..."
                   onkeydown="if(event.key==='Enter'){ this.form.submit(); }">
            </input>
        </div>

        <select name="filter"
                class="form-select"
                style="width: 220px; border: 1px solid;"
                onchange="this.form.submit()">

            <option value="">All Inventory</option>

            <option value="out_of_stock" @selected(request('filter')=='out_of_stock')>
                Out of Stock
            </option>

            <option value="critical_stock" @selected(request('filter')=='critical_stock')>
                Low Stock (≤ Threshold)
            </option>

            <option value="low_stock" @selected(request('filter')=='low_stock')>
                Semi Low
            </option>

            <option value="healthy_stock" @selected(request('filter')=='healthy_stock')>
                Well Stocked
            </option>

            <option value="well_stocked" @selected(request('filter')=='well_stocked')>
                Over Stocked
            </option>

        </select>

    </form>
</div>

{{-- TABLE --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Adjust</th>
                </tr>
            </thead>

            <tbody>
            @foreach($products as $product)

                @php
                    [$label, $color] = stockStatus($product);
                @endphp

                <tr class="{{ $product->stock_quantity == 0 ? 'table-danger' : '' }}">

                    <td class="fw-semibold">{{ $product->name }}</td>
                    <td>{{ $product->category?->name ?? '—' }}</td>
                    <td>{{ $product->stock_quantity }}</td>

                    <td>
                        <span class="badge bg-{{ $color }}">
                            {{ $label }}
                        </span>
                    </td>

                    <td>
                        <form action="{{ route('coffeeshop.inventory.adjust', $product) }}"
                              method="POST"
                              class="d-flex gap-2">

                            @csrf

                            <select name="adjustment_type" class="form-select form-select-sm" style="width:120px;">
                                <option value="restock">Restock</option>
                                <option value="adjustment">Adjust</option>
                            </select>

                            <input type="number"
                                   name="quantity"
                                   class="form-control form-control-sm"
                                   style="width:80px;"
                                   required>

                            <input type="text"
                                   name="notes"
                                   class="form-control form-control-sm"
                                   placeholder="Notes">

                            <button class="btn btn-sm btn-primary">
                                Apply
                            </button>

                        </form>
                    </td>

                </tr>

            @endforeach
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
        <div class="card-footer">
            {{ $products->links() }}
        </div>
    @endif
</div>

@endsection