@extends('layouts.app')

@section('title', 'Products')
@section('pageTitle', 'Product Management')
@section('pageSubtitle', 'Manage menu items, pricing, stock, and categories')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="coffeeshop-page-shell">
    <div class="coffeeshop-hero">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
            <div>
                <div class="fw-bold fs-5">Menu design and product control</div>
                <div class="opacity-75 mt-1">Fine-tune your drinks, pricing, and availability from one calm workspace.</div>
            </div>
            <a href="{{ route('coffeeshop.products.create') }}" class="btn btn-light rounded-pill px-3 fw-semibold">
                <i class="fa-solid fa-plus me-2"></i>Add Product
            </a>
        </div>
    </div>

    <form class="d-flex align-items-center gap-2 flex-wrap justify-content-end" method="GET" id="productsFilterForm">

        <!-- SEARCH -->
        <div style="width: 320px;">
            <div class="input-group coffeeshop-form-control" style="border: 1px solid black; border-radius: 6px; height: 45px;">
                <span class="input-group-text bg-white border-0 px-3">
                    <i class="fa-solid fa-magnifying-glass text-muted fs-5"></i>
                </span>
                <input type="text" name="search" id="productSearch" value="{{ request('search') }}" class="form-control border-0 shadow-none py-2" placeholder="Search products..." autocomplete="off" style="font-size: 1.05rem;">
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
                @if(request('category_id') && request('category_id') !== 'all' || request('status') && request('status') !== 'all')
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                 onclick="event.stopPropagation()"
                 style="min-width: 280px; border-radius: 8px; z-index: 1055;">

                <!-- Category -->
                <label class="form-label small mb-1 fw-semibold text-muted">Category</label>
                <select name="category_id" class="form-select mb-3 shadow-none" style="height:38px; border-radius:4px; border: 1px solid black;">
                    <option value="all">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}" @selected(request('category_id') == $category->category_id)>{{ $category->name }}</option>
                    @endforeach
                </select>

                <!-- Status -->
                <label class="form-label small mb-1 fw-semibold text-muted">Status</label>
                <select name="status" class="form-select mb-3 shadow-none" style="height:38px; border-radius:4px; border: 1px solid black;">
                    <option value="all" @selected(request('status') === 'all' || !request()->has('status'))>All Statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-50" style="height: 38px;">Apply</button>
                    <a href="{{ route('coffeeshop.products') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center" style="height: 38px;">Reset</a>
                </div>
            </div>
        </div>

    </form>

    <div class="coffeeshop-panel p-3 p-lg-4">

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table mb-0 align-middle coffeeshop-table">
                    <thead><tr><th>Product</th><th>Category</th><th>Description</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="fw-semibold text-brown">{{ $product->name }}</div>
                                @if($product->isManualTracked())
                                    @if($product->isLowStock())
                                        <span class="coffeeshop-pill bg-danger-subtle text-danger">Low Stock</span>
                                    @elseif($product->isSemiLow())
                                        <span class="coffeeshop-pill bg-warning-subtle text-warning">Semi Low</span>
                                    @elseif($product->stock_quantity >= 100)
                                        <span class="coffeeshop-pill bg-primary-subtle text-primary">Over Stocked</span>
                                    @endif
                                @endif
                            </td>
                            <td>{{ $product->category?->name }}</td>
                            <td>{{ Str::limit($product->description, 50) }}</td>
                            <td>₱{{ number_format($product->price, 2) }}</td>
                            <td>
                                @if($product->isNoTracking())
                                    <span class="text-muted fst-italic small">None</span>
                                @else
                                    {{ $product->stock_quantity }}
                                @endif
                            </td>
                            <td><span class="coffeeshop-pill {{ $product->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('coffeeshop.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Edit</a>
                                <form action="{{ route('coffeeshop.products.toggle-active', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @if($product->is_active)
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="swalConfirmToggleProduct(this, false, '{{ addslashes($product->name) }}')">Deactivate</button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="swalConfirmToggleProduct(this, true, '{{ addslashes($product->name) }}')">Activate</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted text-center py-4">No products found.</td></tr>
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
