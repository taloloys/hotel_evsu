@extends('layouts.app')

@section('title', 'Coffee Shop POS')
@section('pageTitle', 'Coffee Shop POS Terminal')
@section('pageSubtitle', 'Open tabs, add items, and checkout when ready')

@section('content')
<div id="pos-alert" class="alert d-none"></div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <div class="d-flex gap-2 flex-wrap" id="category-filters">
                        <button type="button" class="btn btn-dark btn-sm category-btn" data-category="all">All</button>
                        @foreach($categories as $category)
                        <button type="button" class="btn btn-outline-secondary btn-sm category-btn" data-category="{{ $category->category_id }}">{{ $category->name }}</button>
                        @endforeach
                    </div>
                    <div class="input-group" style="max-width: 280px;">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" id="product-search" class="form-control" placeholder="Search products..." autocomplete="off">
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3" id="product-grid">
            @foreach($products as $product)
            <div class="col-md-3 col-6 product-tile" data-product-id="{{ $product->product_id }}" data-category-id="{{ $product->category_id }}">
                <div class="card border-0 shadow-sm h-100 {{ $product->stock_quantity <= 0 ? 'opacity-50' : '' }}">
                    <div class="card-body text-center">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" class="mb-2 rounded" style="max-height:48px;" alt="">
                        @else
                            <i class="fa-solid fa-mug-hot fa-2x text-warning mb-2"></i>
                        @endif
                        <div class="fw-semibold">{{ $product->name }}</div>
                        <small class="text-muted d-block">{{ Str::limit($product->description, 40) }}</small>
                        <small class="text-muted">{{ $product->category?->name }}</small>
                        <div class="fw-bold text-primary mt-2">₱{{ number_format($product->price, 2) }}</div>
                        <small class="{{ $product->isLowStock() ? 'text-danger' : 'text-muted' }}">Stock: {{ $product->stock_quantity }}</small>
                        <button type="button" class="btn btn-primary btn-sm w-100 mt-2 add-product-btn"
                                data-product-id="{{ $product->product_id }}"
                                {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                            ADD
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm sticky-top" style="top:15px;">
            <div class="card-header bg-white">
                <div class="fw-bold">Customer Tabs</div>
                <small class="text-muted">Multiple open tabs supported</small>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2 mb-3 flex-wrap" id="tab-switcher"></div>

                <div class="input-group mb-3">
                    <input type="text" id="new-tab-name" class="form-control form-control-sm" placeholder="Customer name or Room 205">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="open-tab-btn">Open Tab</button>
                </div>

                <div id="active-tab-panel" class="d-none">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong id="active-tab-name"></strong>
                        <span class="badge bg-primary" id="active-tab-total">₱0.00</span>
                    </div>
                    <div id="cart-items" class="small mb-3"></div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                        <span>Total</span>
                        <span id="cart-total">₱0.00</span>
                    </div>
                    <button type="button" class="btn btn-success w-100 mb-2" id="close-tab-btn">
                        <i class="fa-solid fa-cash-register me-1"></i> Close Tab / Pay
                    </button>
                    <button type="button" class="btn btn-outline-danger w-100" id="cancel-tab-btn">Cancel Tab</button>
                </div>

                <div id="no-tab-message" class="text-muted text-center py-4">
                    Open a tab to start taking orders.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="closeTabModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Close Tab & Checkout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Payment Method</label>
                <select id="payment-method" class="form-select mb-3">
                    <option value="cash">Cash Payment</option>
                    <option value="room_charge">Charge to Room</option>
                </select>
                <div id="room-charge-panel" class="d-none">
                    <label class="form-label">Checked-in Guest</label>
                    <select id="checked-in-guest" class="form-select">
                        <option value="">Loading guests...</option>
                    </select>
                    <div id="guest-balance-info" class="small text-muted mt-2"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirm-close-tab">Complete Payment</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.posConfig = {
    csrfToken: @json(csrf_token()),
    routes: {
        search: @json(route('coffeeshop.api.products.search')),
        tabs: @json(route('coffeeshop.api.tabs.index')),
        storeTab: @json(route('coffeeshop.api.tabs.store')),
        guests: @json(route('coffeeshop.api.guests.checked-in')),
        tabItems: @json(url('/coffeeshop/api/tabs')),
        closeTab: @json(url('/coffeeshop/api/tabs')),
        cancelTab: @json(url('/coffeeshop/api/tabs')),
    },
    initialTabs: @json($openTabs->map(fn($tab) => app(\App\Services\Coffeeshop\PosTabService::class)->formatTab($tab)))
};
</script>
<script src="{{ asset('js/coffeeshop/pos-terminal.js') }}"></script>
@endpush
