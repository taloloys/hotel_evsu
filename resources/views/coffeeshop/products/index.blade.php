@extends('layouts.app')

@section('title', 'Products')
@section('pageTitle', 'Product Management')
@section('pageSubtitle', 'Manage menu items, pricing, stock, and categories')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="coffeeshop-page-shell">
    <div class="coffeeshop-hero" style="background: linear-gradient(135deg, #504538 0%, #3a3025 100%);">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
            <div>
                <div class="fw-bold fs-5 font-display" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">Menu design and product control</div>
                <div class="opacity-75 mt-1 font-brand" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">Fine-tune your drinks, pricing, and availability from one calm workspace.</div>
            </div>
            <a href="{{ route('coffeeshop.products.create') }}" class="btn btn-light rounded-pill px-3 fw-semibold font-brand" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">
                <i class="fa-solid fa-plus me-2"></i>Add Product
            </a>
        </div>
    </div>

    <form class="d-flex align-items-center gap-2 flex-wrap justify-content-end mb-3" method="GET" id="productsFilterForm">

        <!-- SEARCH -->
        <div style="width: 320px;">
            <div class="input-group coffeeshop-form-control" style="border: 1px solid #827567; border-radius: 6px; height: 45px; overflow: hidden;">
                <span class="input-group-text bg-white border-0 px-3">
                    <i class="fa-solid fa-magnifying-glass" style="color: #627e71; font-size: 1.05rem;"></i>
                </span>
                <input type="text" name="search" id="productSearch" value="{{ request('search') }}" class="form-control border-0 shadow-none py-2 font-brand" placeholder="Search products..." autocomplete="off" style="font-size: 1.05rem; font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">
            </div>
        </div>

        <!-- FILTER DROPDOWN -->
        <div class="dropdown">
            <button class="btn d-flex align-items-center gap-2 px-3 position-relative font-brand"
                    type="button"
                    data-bs-toggle="dropdown"
                    style="height: 45px; border-radius: 6px; border: 1px solid #827567; color: #504538; background-color: #ffffff; font-size: 1.05rem; font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">
                <i class="fa-solid fa-filter" style="color: #627e71;"></i>
                <span>Filter</span>
                @if(request('category_id') && request('category_id') !== 'all' || request('status') && request('status') !== 'all')
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                 onclick="event.stopPropagation()"
                 style="min-width: 280px; border-radius: 8px; z-index: 1055;">

                <!-- Category -->
                <label class="form-label small mb-1 fw-semibold font-brand" style="color: #827567; font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">Category</label>
                <select name="category_id" class="form-select mb-3 shadow-none font-brand" style="height:38px; border-radius:4px; border: 1px solid #827567; font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">
                    <option value="all">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}" @selected(request('category_id') == $category->category_id)>{{ $category->name }}</option>
                    @endforeach
                </select>

                <!-- Status -->
                <label class="form-label small mb-1 fw-semibold font-brand" style="color: #827567; font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">Status</label>
                <select name="status" class="form-select mb-3 shadow-none font-brand" style="height:38px; border-radius:4px; border: 1px solid #827567; font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">
                    <option value="all" @selected(request('status') === 'all' || !request()->has('status'))>All Statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn text-white w-50 font-brand fw-bold" style="height: 38px; background-color: #334c42; border: none; font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">Apply</button>
                    <a href="{{ route('coffeeshop.products') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center font-brand" style="height: 38px; font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">Reset</a>
                </div>
            </div>
        </div>

    </form>

    <div class="coffeeshop-panel p-3 p-lg-4">

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table mb-0 align-middle coffeeshop-table">
                    <thead style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                        <tr>
                            <th style="font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif; color: #111827; font-size: 0.95rem; font-weight: 700; padding: 1rem 1rem;">Product</th>
                            <th style="font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif; color: #111827; font-size: 0.95rem; font-weight: 700; padding: 1rem 1rem;">Category</th>
                            <th style="font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif; color: #111827; font-size: 0.95rem; font-weight: 700; padding: 1rem 1rem;">Description</th>
                            <th style="font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif; color: #111827; font-size: 0.95rem; font-weight: 700; padding: 1rem 1rem;">Price</th>
                            <th style="font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif; color: #111827; font-size: 0.95rem; font-weight: 700; padding: 1rem 1rem;">Stock</th>
                            <th style="font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif; color: #111827; font-size: 0.95rem; font-weight: 700; padding: 1rem 1rem;">Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody style="font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif;">
                    @forelse($products as $product)
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 1.1rem 1rem;">
                                <div class="fw-bold" style="color: #111827; font-size: 1.05rem; line-height: 1.3;">{{ $product->name }}</div>
                                @if($product->isManualTracked())
                                    <div class="mt-1">
                                        @if($product->isLowStock())
                                            <span class="badge rounded-pill bg-danger-subtle text-danger fw-semibold" style="font-size: 0.78rem; padding: 0.25rem 0.65rem;">Low Stock</span>
                                        @elseif($product->isSemiLow())
                                            <span class="badge rounded-pill bg-warning-subtle text-warning fw-semibold" style="font-size: 0.78rem; padding: 0.25rem 0.65rem;">Semi Low</span>
                                        @elseif($product->stock_quantity >= 100)
                                            <span class="badge rounded-pill fw-semibold" style="background-color: #627e71; color: #ffffff; font-size: 0.78rem; padding: 0.25rem 0.65rem;">Over Stocked</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 1.1rem 1rem;"><span class="fw-bold" style="color: #111827; font-size: 1.02rem;">{{ $product->category?->name }}</span></td>
                            <td style="padding: 1.1rem 1rem;"><span style="color: #4b5563; font-size: 0.98rem;">{{ Str::limit($product->description, 50) }}</span></td>
                            <td style="padding: 1.1rem 1rem;"><span class="fw-bold" style="color: #111827; font-size: 1.08rem;">₱{{ number_format($product->price, 2) }}</span></td>
                            <td style="padding: 1.1rem 1rem;">
                                @if($product->isNoTracking())
                                    <span class="fst-italic text-muted" style="font-size: 0.95rem;">None</span>
                                @else
                                    <span class="fw-bold" style="color: #111827; font-size: 1.05rem;">{{ $product->stock_quantity }}</span>
                                @endif
                            </td>
                            <td style="padding: 1.1rem 1rem;"><span class="badge rounded-pill {{ $product->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} fw-semibold" style="font-size: 0.85rem; padding: 0.3rem 0.75rem;">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td class="text-end" style="padding: 1.1rem 1rem;">
                                <a href="{{ route('coffeeshop.products.edit', $product) }}" class="btn rounded-pill px-3 fw-semibold" style="border: 1px solid #827567; color: #504538; font-size: 0.92rem; padding: 0.35rem 0.95rem;">Edit</a>
                                <form action="{{ route('coffeeshop.products.toggle-active', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @if($product->is_active)
                                        <button type="button" class="btn btn-outline-danger rounded-pill px-3 fw-semibold" style="font-size: 0.92rem; padding: 0.35rem 0.95rem;" onclick="swalConfirmToggleProduct(this, false, '{{ addslashes($product->name) }}')">Deactivate</button>
                                    @else
                                        <button type="button" class="btn btn-outline-success rounded-pill px-3 fw-semibold" style="font-size: 0.92rem; padding: 0.35rem 0.95rem;" onclick="swalConfirmToggleProduct(this, true, '{{ addslashes($product->name) }}')">Activate</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted text-center py-4 fs-5">No products found.</td></tr>
                    @endforelse
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
    window.swalConfirmToggleProduct = function(btn, activate, productName) {
        var form = btn.closest('form');
        Swal.fire({
            icon: activate ? 'question' : 'warning',
            title: activate ? 'Activate Product?' : 'Deactivate Product?',
            html: (activate ? 'Activate' : 'Deactivate') + ' <strong>' + productName + '</strong>?',
            showCancelButton: true,
            confirmButtonText: activate
                ? '<i class="fa-solid fa-circle-check me-1"></i> Activate'
                : '<i class="fa-solid fa-circle-xmark me-1"></i> Deactivate',
            cancelButtonText: 'Cancel',
            confirmButtonColor: activate ? '#198754' : '#dc3545',
            reverseButtons: true,
        }).then(function(result) {
            if (result.isConfirmed && form) {
                form.submit();
            }
        });
    };

    // Debounce auto-submit for server-side search input
    (function () {
        const searchInput = document.getElementById('productSearch');
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
        const form = document.getElementById('productsFilterForm');
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
