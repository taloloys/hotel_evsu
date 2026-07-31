@extends('layouts.app')

@section('title', 'Inventory')
@section('pageTitle', 'Inventory Management')
@section('pageSubtitle', 'Track stock levels, restock items, and monitor alerts')

@section('content')
@include('coffeeshop.partials.alerts')

@php
    if (!function_exists('stockStatus')) {
        function stockStatus($product) {
            $qty = $product->stock_quantity;
            $threshold = $product->effectiveLowStockThreshold();
            if ($qty == 0) return ['Out of Stock', 'danger'];
            if ($qty <= $threshold) return ['Low Stock', 'danger'];
            if ($qty <= (int)($threshold * 1.4)) return ['Semi Low', 'warning'];
            if ($qty <= (int)($threshold * 2)) return ['Well Stocked', 'success'];
            return ['Over Stocked', 'primary'];
        }
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
        <div class="col-md-4"><div class="coffeeshop-card p-4 h-100"><div class="text-muted small">Tracked Products</div><div class="fs-3 fw-bold text-brown">{{ $products->total() }}</div></div></div>
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

    <form method="GET" class="d-flex align-items-center gap-2 flex-wrap justify-content-end" id="inventoryFilterForm">

        <!-- SEARCH -->
        <div style="width: 320px;">
            <div class="input-group coffeeshop-form-control" style="border: 1px solid black; border-radius: 6px; height: 45px;">
                <span class="input-group-text bg-white border-0 px-3">
                    <i class="fa-solid fa-magnifying-glass text-muted fs-5"></i>
                </span>
                <input type="text" name="search" id="inventorySearch" value="{{ request('search') }}" class="form-control border-0 shadow-none py-2" placeholder="Search products..." autocomplete="off" style="font-size: 1.05rem;">
            </div>
        </div>

        <!-- FILTER DROPDOWN -->
        <div class="dropdown">
            <button class="btn btn-outline-dark d-flex align-items-center gap-2 px-3 position-relative"
                    type="button"
                    data-bs-toggle="dropdown"
                    style="height: 45px; border-radius: 6px; border: 1px solid black; font-size: 1.05rem;">
                <i class="fa-solid fa-filter fs-5"></i>
                <span>Filter</span>
                @if(request('filter'))
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                 onclick="event.stopPropagation()"
                 style="min-width: 280px; border-radius: 8px; z-index: 1055;">

                <!-- Stock Status -->
                <label class="form-label small mb-1 fw-semibold text-muted">Stock Status</label>
                <select name="filter" class="form-select mb-3 shadow-none" style="height:38px; border-radius:4px; border: 1px solid black;">
                    <option value="">All Inventory</option>
                    <option value="out_of_stock" @selected(request('filter')=='out_of_stock')>Out of Stock</option>
                    <option value="critical_stock" @selected(request('filter')=='critical_stock')>Low Stock (≤ Threshold)</option>
                    <option value="low_stock" @selected(request('filter')=='low_stock')>Semi Low</option>
                    <option value="healthy_stock" @selected(request('filter')=='healthy_stock')>Well Stocked</option>
                    <option value="well_stocked" @selected(request('filter')=='well_stocked')>Over Stocked</option>
                </select>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-50" style="height: 38px;">Apply</button>
                    <a href="{{ route('coffeeshop.inventory') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center" style="height: 38px;">Reset</a>
                </div>
            </div>
        </div>

    </form>

    <div class="coffeeshop-panel p-2 p-lg-4">

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
                                    <input type="number" name="quantity" class="form-control form-control-sm rounded-pill" placeholder="Qty" min="1" style="width:120px; border: 1px solid black;" required>
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

@push('scripts')
<script>
    (function () {
        const searchInput = document.getElementById('inventorySearch');
        if (searchInput) {
            function debounce(func, wait) {
                let timeout;
                return function (...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            const form = searchInput.closest('form');
            if (form) {
                searchInput.addEventListener('input', debounce(function () {
                    if (form.requestSubmit) {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                }, 500));
            }
        }
    })();

    // Close dropdown on form submit (handles Turbo dynamic page load preservation)
    (function () {
        const form = document.getElementById('inventoryFilterForm');
        if (form) {
            form.addEventListener('submit', function () {
                const dropdownEl = form.querySelector('[data-bs-toggle="dropdown"]');
                if (dropdownEl) {
                    try {
                        const dropdown = bootstrap.Dropdown.getOrCreateInstance(dropdownEl);
                        if (dropdown) {
                            dropdown.hide();
                        }
                    } catch (e) {}
                    dropdownEl.classList.remove('show');
                    dropdownEl.setAttribute('aria-expanded', 'false');
                    const menu = dropdownEl.nextElementSibling;
                    if (menu) {
                        menu.classList.remove('show');
                    }
                }
            });
        }
    })();
</script>
@endpush