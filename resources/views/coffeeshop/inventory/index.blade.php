@extends('layouts.app')

@section('title', 'Inventory')
@section('pageTitle', 'Inventory Management')
@section('pageSubtitle', 'Track stock levels, restock items, and monitor alerts')

@section('content')
@include('coffeeshop.partials.alerts')

{{-- INVENTORY SUMMARY HEADER --}}
<div class="row g-3 mb-4">

    {{-- Default Threshold --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <div class="text-muted small">Low Stock Threshold</div>
                    <div class="fs-4 fw-bold">
                        {{ $defaultThreshold }}
                    </div>
                </div>

                <i class="fa-solid fa-sliders text-warning fs-3"></i>

            </div>
        </div>
    </div>

    {{-- Items Needing Restock --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <div class="text-muted small">Needs Restocking</div>
                    <div class="fs-4 fw-bold text-danger">
                        {{ $lowStockProducts->count() }}
                    </div>
                </div>

                <i class="fa-solid fa-triangle-exclamation text-danger fs-3"></i>

            </div>
        </div>
    </div>

    {{-- Critical Items (NEW - VERY USEFUL) --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <div class="text-muted small">Critical Stock (≤ 5)</div>
                    <div class="fs-4 fw-bold text-danger">
                        {{ $lowStockProducts->where('stock_quantity', '<=', 5)->count() }}
                    </div>
                </div>

                <i class="fa-solid fa-bell text-danger fs-3"></i>

            </div>
        </div>
    </div>

</div>

@if($lowStockProducts->isNotEmpty())
<div class="alert alert-danger">
    <strong>Low stock alert:</strong>
    {{ $lowStockProducts->pluck('name')->join(', ') }}
</div>
@endif

<div class="d-flex justify-content-end align-items-center mb-4">

    <form method="GET"
          id="inventoryFilterForm"
          class="d-flex align-items-center gap-3">

        {{-- Search --}}
        <div class="input-group" style="width: 460px; height: 50px;">
            <span class="input-group-text bg-white px-3">
                <i class="fa-solid fa-magnifying-glass text-muted"></i>
            </span>

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="form-control py-3"
                placeholder="Search inventory..."
                autocomplete="off"
                onkeydown="if(event.key==='Enter'){ this.form.submit(); }">
        </div>

        {{-- Filter --}}
        <select
            name="filter"
            class="form-select py-2"
            style="width: 240px; height: 50px;"
            onchange="this.form.submit()">

            <option value="">All Inventory</option>

            <option value="well_stocked"
                @selected(request('filter') == 'well_stocked')>
                Well Stocked
            </option>

            <option value="low_stock"
                @selected(request('filter') == 'low_stock')>
                Low Stock
            </option>

            <option value="out_of_stock"
                @selected(request('filter') == 'out_of_stock')>
                Out of Stock
            </option>

        </select>

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
