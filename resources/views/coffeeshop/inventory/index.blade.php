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

    <form method="GET" class="d-flex align-items-center gap-2 flex-wrap justify-content-end mb-3" id="inventoryFilterForm">

        <!-- SEARCH -->
        <div style="width: 340px;">
            <div class="input-group coffeeshop-form-control shadow-sm" style="border: 1px solid #c2a889; border-radius: 0.5rem; overflow: hidden; height: 45px; background-color: #ffffff;">
                <span class="input-group-text bg-white border-0 px-3">
                    <i class="fa-solid fa-magnifying-glass" style="color: #627e71;"></i>
                </span>
                <input type="text" name="search" id="inventorySearch" value="{{ request('search') }}" class="form-control border-0 shadow-none py-2" placeholder="Search products..." autocomplete="off" style="font-size: 1rem; font-family: 'Lucida Fax', 'Georgia', serif; color: #504538;">
            </div>
        </div>

        <!-- FILTER DROPDOWN -->
        <div class="dropdown">
            <button class="btn bg-white d-flex align-items-center gap-2 px-3 position-relative shadow-sm"
                    type="button"
                    data-bs-toggle="dropdown"
                    style="height: 45px; border-radius: 0.5rem; border: 1px solid #c2a889; color: #504538; font-size: 1rem; font-family: 'Lucida Fax', serif;">
                <i class="fa-solid fa-filter" style="color: #627e71;"></i>
                <span class="fw-semibold">Filter</span>
                @if(request('filter'))
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                 onclick="event.stopPropagation()"
                 style="min-width: 280px; border-radius: 0.75rem; z-index: 1055; font-family: 'Lucida Fax', 'Georgia', serif;">

                <!-- Stock Status -->
                <label class="form-label small mb-1 fw-semibold text-muted" style="font-family: 'Franklin Gothic Medium', sans-serif;">Stock Status</label>
                <select name="filter" class="form-select mb-3 shadow-none" style="height:38px; border-radius:0.5rem; border: 1px solid #827567;">
                    <option value="">All Inventory</option>
                    <option value="out_of_stock" @selected(request('filter')=='out_of_stock')>Out of Stock</option>
                    <option value="critical_stock" @selected(request('filter')=='critical_stock')>Low Stock (≤ Threshold)</option>
                    <option value="low_stock" @selected(request('filter')=='low_stock')>Semi Low</option>
                    <option value="healthy_stock" @selected(request('filter')=='healthy_stock')>Well Stocked</option>
                    <option value="well_stocked" @selected(request('filter')=='well_stocked')>Over Stocked</option>
                </select>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn text-white w-50 fw-semibold" style="height: 38px; background-color: #334c42; border: none;">Apply</button>
                    <a href="{{ route('coffeeshop.inventory') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center fw-semibold" style="height: 38px; border: 1px solid #827567; color: #504538;">Reset</a>
                </div>
            </div>
        </div>

    </form>

    <div class="coffeeshop-panel p-2 p-lg-4">

        <div class="card border-1 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table align-middle mb-0 coffeeshop-table">
                    <thead style="background-color: #f8f3ed; border-bottom: 1px solid #e5e7eb; color: #2c241d; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif; font-size: 0.90rem; font-weight: 700; text-transform: uppercase;">
                        <tr><th style="color: #2c241d; padding: 0.9rem 1rem;">Product</th><th style="color: #2c241d; padding: 0.9rem 1rem;">Category</th><th style="color: #2c241d; padding: 0.9rem 1rem;">Stock</th><th style="color: #2c241d; padding: 0.9rem 1rem;">Status</th><th style="color: #2c241d; padding: 0.9rem 1rem;">Adjust</th></tr>
                    </thead>
                    <tbody style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                    @foreach($products as $product)
                        @php [$label, $color] = stockStatus($product); @endphp
                        <tr class="{{ $product->stock_quantity == 0 ? 'table-danger' : '' }}" style="border-bottom: 1px solid #f0f0f0;">
                            <td style="padding: 1.05rem 1rem; color: #2c241d; font-weight: 600; font-size: 1.05rem;">{{ $product->name }}</td>
                            <td style="padding: 1.05rem 1rem; color: #382e25; font-weight: 500; font-size: 1.02rem;">{{ $product->category?->name ?? '—' }}</td>
                            <td style="padding: 1.05rem 1rem; color: #2c241d; font-weight: 600; font-size: 1.04rem;">{{ $product->stock_quantity }}</td>
                            <td style="padding: 1.05rem 1rem;"><span class="coffeeshop-pill fw-semibold bg-{{ $color }}-subtle text-{{ $color }}" style="font-size: 0.88rem; padding: 0.28rem 0.8rem;">{{ $label }}</span></td>
                            <td style="padding: 1.05rem 1rem;">
                                <form action="{{ route('coffeeshop.inventory.adjust', $product) }}" method="POST" class="d-flex gap-2 align-items-center flex-nowrap">
                                    @csrf
                                    <select name="adjustment_type"
                                            class="form-select rounded-pill inventory-type-select"
                                            style="width:145px; border: 1px solid #827567; font-size: 0.90rem; padding: 0.4rem 0.8rem;"
                                            data-stock="{{ $product->stock_quantity }}">
                                        <option value="restock">Restock (+)</option>
                                        <option value="adjustment">Adjust (=)</option>
                                    </select>
                                    <input type="number"
                                           name="quantity"
                                           class="form-control rounded-pill inventory-qty-input"
                                           placeholder="Add Qty"
                                           min="1"
                                           style="width:115px; border: 1px solid #827567; font-size: 0.90rem; padding: 0.4rem 0.8rem;"
                                           required>
                                    <input type="text" name="notes" class="form-control rounded-pill" placeholder="Notes (optional)" style="width:260px; border: 1px solid #827567; font-size: 0.90rem; padding: 0.4rem 0.8rem;">
                                    <button type="submit" class="btn text-white rounded-pill px-4 fw-semibold text-nowrap shadow-sm" style="background-color: #334c42; border: none; font-size: 0.94rem; padding: 0.42rem 1.2rem;">Apply</button>
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
        // ── Restock vs Adjust input semantics ─────────────────────────────────
        // Restock: add units on top of current stock  → min=1, placeholder="Add Qty"
        // Adjust:  set stock to an absolute new value → min=0, placeholder="New Total"
        document.querySelectorAll('.inventory-type-select').forEach(function (select) {
            const form    = select.closest('form');
            const qtyInput = form ? form.querySelector('.inventory-qty-input') : null;
            if (!qtyInput) { return; }

            function syncInput() {
                const isAdjust = select.value === 'adjustment';
                if (isAdjust) {
                    qtyInput.min         = '0';
                    qtyInput.placeholder = 'New Total';
                    qtyInput.title       = 'Enter the desired total stock level';
                } else {
                    qtyInput.min         = '1';
                    qtyInput.placeholder = 'Add Qty';
                    qtyInput.title       = 'Enter the number of units to add';
                }
                // Clear the value so the user consciously enters the right number
                qtyInput.value = '';
            }

            select.addEventListener('change', syncInput);
        });

        // ── Search debounce ───────────────────────────────────────────────────
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