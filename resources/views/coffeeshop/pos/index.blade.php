@extends('layouts.app')

@section('title', 'Coffee Shop POS')
@section('pageTitle', 'Coffee Shop POS Terminal')
@section('pageSubtitle', 'Open tabs, add items, and checkout when ready')

@section('content')
<div id="pos-alert" class="alert d-none"></div>

<div class="row g-3">
    <div class="col-lg-8">

        <!-- Suggested Pairings -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-light">
                <strong>
                    <i class="fa-solid fa-lightbulb text-warning me-2"></i>
                    Suggested Pairings
                </strong>
            </div>

            <div class="card-body">

                <div id="suggested-pairings" class="d-flex flex-wrap gap-2">

                    <button class="btn btn-outline-success btn-sm pairing-btn"
                        data-items="Americano,Cookies">
                        Americano + Cookies
                    </button>

                    <button class="btn btn-outline-success pairing-btn" data-items="Cappuccino,Cookies">
                        Cappuccino + Cookies
                    </button>

                    <button class="btn btn-outline-success btn-sm pairing-btn" data-items="Latte,Cookies">
                        Latte + Cookies
                    </button>

                    <button class="btn btn-outline-success btn-sm pairing-btn" data-items="Americano,Fresh Milk">
                        Americano + Fresh Milk
                    </button>

                </div>

            </div>
        </div>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <div class="d-flex gap-2 flex-wrap" id="category-filters">
                        <button type="button" class="btn btn-dark px-4 py-2 btn-sm category-btn" data-category="all">All</button>
                        @foreach($categories as $category)
                        <button type="button" class="btn btn-outline-secondary btn-sm category-btn" data-category="{{ $category->category_id }}">{{ $category->name }}</button>
                        @endforeach
                    </div>
                    <div class="input-group" style="max-width: 400px;">
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
                        <button type="button" class="btn btn-primary btn-sm w-100 px-4 py-2 mt-2 add-product-btn"
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
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-bold">Customer Tabs</div>
                    <small class="text-muted">Multiple open tabs supported</small>
                </div>

                <span class="badge bg-primary fs-6">
                    <i class="fa-solid fa-folder-open me-1"></i>
                    <span id="open-tabs-counter">{{ count($openTabs) }}</span> Open
                </span>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2 mb-3 flex-wrap" id="tab-switcher"></div>

                <div class="card bg-light border-0 p-3 mb-5 ">
                    <div class="mb-3">
                    <label class="form-label fw-semibold text-muted mb-2">
                        Tab Type
                    </label>

                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="new-tab-type" id="type-walkin" value="walk_in" checked>
                        <label class="btn btn-outline-secondary py-2 px-3" for="type-walkin">
                            Walk-in
                        </label>

                        <input type="radio" class="btn-check" name="new-tab-type" id="type-room" value="room">
                        <label class="btn btn-outline-secondary py-2 px-3" for="type-room">
                            Room Charge
                        </label>
                    </div>
                </div>
                

                    <!-- Walk-in input panel -->
                    <div id="new-tab-walkin-panel" class="mb-2">
                        <input type="text" id="new-tab-name" class="form-control py-3" placeholder="Customer name (e.g. John Doe)">
                    </div>

                    <!-- Checked-in Room input panel (hidden by default) -->
                    <div id="new-tab-room-panel" class="mb-2 d-none">
                        <select id="new-tab-guest" class="form-select py-3">
                            <option value="">Select occupied room...</option>
                        </select>
                    </div>

                    <button type="button" class="btn btn-primary w-100 py-2 fw-semibold" id="open-tab-btn">
                        <i class="fa-solid fa-folder-open me-1"></i> Open Tab
                    </button>
                </div>

                <div id="active-tab-panel" class="d-none">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong id="active-tab-name"></strong>
                            <div id="active-tab-badge" class="mt-1"></div>
                        </div>
                        <span class="badge bg-primary" id="active-tab-total">₱0.00</span>
                    </div>
                

                    <!-- Alert message if there is a pending cancel request -->
                    <div id="active-tab-pending-alert" class="alert alert-warning py-2 px-3 small d-none my-2">
                        <i class="fa-solid fa-clock me-1"></i> Cancellation Pending Authorization
                    </div>

                    <div id="cart-items" class="small mb-3"></div>
                    <div class="mt-3">
                        <label class="fw-semibold text-muted mb-1">
                            Item Notes
                        </label>

                        <textarea
                            id="item-notes"
                            class="form-control"
                            rows="2"
                            placeholder="Less sugar, no ice, extra shot, etc.">
                        </textarea>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                        <span>Total</span>
                        <span id="cart-total">₱0.00</span>
                    </div>

                    <!-- Walk-in actions -->
                    <div id="walkin-checkout-actions" class="d-none">
                        <button type="button" class="btn btn-success w-100 mb-2 checkout-action-btn" id="pay-walkin-btn">
                            <i class="fa-solid fa-cash-register me-1"></i> Pay / Close Tab
                        </button>
                    </div>

                    <!-- Room charge actions -->
                    <div id="room-checkout-actions" class="d-none">
                        <button type="button" class="btn btn-success w-100 mb-2 checkout-action-btn" id="charge-room-btn">
                            <i class="fa-solid fa-hotel me-1"></i> Charge to Room
                        </button>
                        <button type="button" class="btn btn-outline-success w-100 mb-2 checkout-action-btn" id="pay-direct-btn">
                            <i class="fa-solid fa-cash-register me-1"></i> Pay Directly
                        </button>
                    </div>

                    <button type="button" class="btn btn-outline-danger w-100 checkout-action-btn" id="cancel-tab-btn">Cancel Tab</button>
                </div>

                <div id="no-tab-message" class="text-muted text-center py-4">
                    Open a tab to start taking orders.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="fa-solid fa-wallet me-2 text-warning"></i>
                    Settle Payment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">

                <p class="text-muted small text-center mb-3">
                    Select the payment method to settle this order of
                    <strong id="payment-modal-amount" class="text-primary">₱0.00</strong>.
                </p>

                <!-- Payment Methods -->
                <div class="d-flex flex-column gap-3">

                    <button type="button"
                            class="btn btn-outline-secondary p-3 text-start d-flex align-items-center justify-content-between payment-method-opt"
                            data-method="cash">

                        <span class="fs-5 fw-semibold">
                            <i class="fa-solid fa-money-bill-wave text-success me-3"></i>
                            Cash
                        </span>

                        <i class="fa-solid fa-chevron-right text-muted"></i>

                    </button>

                    <button type="button"
                            class="btn btn-outline-secondary p-3 text-start d-flex align-items-center justify-content-between payment-method-opt"
                            data-method="gcash">

                        <span class="fs-5 fw-semibold">
                            <i class="fa-solid fa-mobile-screen text-info me-3"></i>
                            GCash
                        </span>

                        <i class="fa-solid fa-chevron-right text-muted"></i>

                    </button>

                    <button type="button"
                            class="btn btn-outline-secondary p-3 text-start d-flex align-items-center justify-content-between payment-method-opt"
                            data-method="card">

                        <span class="fs-5 fw-semibold">
                            <i class="fa-solid fa-credit-card text-primary me-3"></i>
                            Card
                        </span>

                        <i class="fa-solid fa-chevron-right text-muted"></i>

                    </button>

                </div>

                <!-- Cash Calculator (Add Here) -->
                <div id="cash-calculator" class="mt-4 d-none">

                    <label class="form-label">
                        Cash Received
                    </label>

                    <input
                        type="number"
                        id="cash-received"
                        class="form-control"
                        placeholder="Enter amount">

                    <div class="mt-3">

                        <h6>
                            Change
                            <span
                                id="cash-change"
                                class="float-end text-success">

                                ₱0.00

                            </span>
                        </h6>

                    </div>
                    <button
                        type="button"
                        id="confirm-cash-payment"
                        class="btn btn-success w-100 mt-3">

                        Confirm Cash Payment

                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cancelTabModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation me-2"></i>Cancel Tab</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p id="cancel-tab-warning-text" class="text-muted small mb-3">Are you sure you want to cancel this tab? All added items will be returned to stock.</p>
                <div id="cancel-reason-container" class="d-none">
                    <label for="cancel-reason" class="form-label small fw-semibold text-danger">Reason for Cancellation (Admin Override Required)</label>
                    <textarea id="cancel-reason" class="form-control" rows="3" placeholder="e.g. Wrong items entered, customer walked away, etc."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Go Back</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirm-cancel-tab-btn">Confirm Cancellation</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmRoomChargeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fa-solid fa-hotel me-2"></i>Charge to Room</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p id="room-charge-warning-text" class="text-muted small mb-0">Are you sure you want to charge the total amount to the guest room folio?</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-sm text-white" id="confirm-room-charge-submit-btn">Confirm Charge</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="posAlertModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="posAlertModalLabel"><i class="fa-solid fa-triangle-exclamation me-2"></i>Notice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p id="pos-alert-modal-message" class="mb-0"></p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3">
            <div id="receipt-content"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.posConfig = {
    csrfToken: @json(csrf_token()),
    isAdmin: @json(auth()->user()?->role?->role_name === 'ADMIN'),
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
