@extends('layouts.app')

@section('title', 'Coffee Shop POS')
@section('pageTitle', 'Coffee Shop POS Terminal')
@section('pageSubtitle', 'Open tabs, add items, and checkout when ready')

@section('content')
<div id="pos-alert" class="alert d-none"></div>

<div class="coffeeshop-page-shell">
    <div class="coffeeshop-hero">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
            <div>
                <div class="fw-bold fs-5">Coffee shop POS</div>
                <div class="opacity-75 mt-1">Create tabs, add drinks and snacks, and check out with a streamlined flow.</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
    <div class="col-lg-8">

        <!-- Suggested Pairings -->
        @if(!empty($suggestedPairings))
        <div class="coffeeshop-card mb-3 p-3">
            <div class="card-header border-0 bg-transparent">
                <strong>
                    <i class="fa-solid fa-lightbulb text-warning me-2"></i>
                    Suggested Pairings
                </strong>
            </div>

            <div class="card-body">

                <div id="suggested-pairings" class="d-flex flex-wrap gap-2">

                    @foreach($suggestedPairings as $pairing)
                        @php
                            $items = explode(',', $pairing);
                            $displayName = implode(' + ', $items);
                        @endphp
                        <button class="btn btn-outline-success btn-sm pairing-btn" data-items="{{ $pairing }}">
                            {{ $displayName }}
                        </button>
                    @endforeach

                </div>

            </div>
        </div>
        @endif
        <div class="coffeeshop-card mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <div class="d-flex gap-2 flex-wrap" id="category-filters">
                        <button type="button" class="btn btn-dark px-4 py-2 btn-sm category-btn" data-category="all">All</button>
                        @foreach($categories as $category)
                        <button type="button" class="btn btn-outline-secondary btn-sm category-btn" data-category="{{ $category->category_id }}">{{ $category->name }}</button>
                        @endforeach
                    </div>
                    <div class="input-group coffeeshop-form-control" style="max-width: 360px; border: 1px solid; overflow: hidden; border-radius: 0.75rem;">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" id="product-search" class="form-control border-0 shadow-none" placeholder="Search products..." autocomplete="off">
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3" id="product-grid">
            @foreach($products as $product)
            <div class="col-md-3 col-6 product-tile"
                data-product-id="{{ $product->product_id }}"
                data-category-id="{{ $product->category_id }}">

                <div class="coffeeshop-card h-100 {{ ($product->is_stockable && $product->stock_quantity <= 0) ? 'opacity-50' : '' }}">
                    <div class="card-body d-flex flex-column text-center">

                        @if($product->image_url)
                            <div class="mb-3 rounded w-100 overflow-hidden" style="height: 120px;">
                                <img src="{{ $product->image_url }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $product->name }}" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-100 h-100 d-flex align-items-center justify-content-center border rounded-3\' style=\'background: linear-gradient(135deg, #fbf7f2 0%, #f4eae0 100%);\'><i class=\'fa-solid fa-mug-hot fa-2x\' style=\'color: #a97142; opacity: 0.6;\'></i></div>';">
                            </div>
                        @else
                            <div class="mb-3 rounded-3 w-100 d-flex align-items-center justify-content-center border" style="height: 120px; background: linear-gradient(135deg, #fbf7f2 0%, #f4eae0 100%);">
                                <i class="fa-solid fa-mug-hot fa-2x" style="color: #a97142; opacity: 0.6;"></i>
                            </div>
                        @endif

                        <div class="fw-semibold">{{ $product->name }}</div>

                        <small class="text-muted d-block" style="min-height:48px;">
                            {{ Str::limit($product->description, 40) }}
                        </small>

                        <small class="text-muted mb-2">
                            {{ $product->category?->name }}
                        </small>

                        <div class="fw-bold text-primary">
                            ₱{{ number_format($product->price, 2) }}
                        </div>

                        @if(!$product->is_stockable)
                            <small class="text-success">Available</small>
                        @elseif($product->stock_quantity <= 0)
                            <small class="text-danger">Sold out</small>
                        @else
                            <small class="text-muted">Stock: {{ $product->stock_quantity }}</small>
                        @endif

                        <button
                            type="button"
                            class="btn btn-primary w-100 mt-auto add-product-btn"
                            data-product-id="{{ $product->product_id }}"
                            {{ ($product->is_stockable && $product->stock_quantity <= 0) ? 'disabled' : '' }}>
                            ADD
                        </button>

                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="col-lg-4">
        <div class="coffeeshop-card sticky-top pos-tabs-floating-card shadow-sm" style="top:15px; display:flex; flex-direction:column; max-height: calc(100vh - 30px); height: calc(100vh - 30px);">
            <!-- Fixed Top Header -->
            <div class="card-header border-bottom bg-white d-flex justify-content-between align-items-center p-3 flex-shrink-0" style="z-index: 5;">
                <div>
                    <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-users text-primary"></i> Customer Tabs
                    </div>
                    <small class="text-muted">Multiple open tabs supported</small>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary fs-6 shadow-sm">
                        <i class="fa-solid fa-folder-open me-1"></i>
                        <span id="open-tabs-counter">{{ count($openTabs) }}</span> Open
                    </span>
                </div>
            </div>

            <!-- Sticky Tab Switcher Strip -->
            <div class="px-3 pt-2 pb-2 bg-light border-bottom flex-shrink-0">
                <div class="d-flex gap-2 flex-wrap" id="tab-switcher"></div>
            </div>

            <!-- Scrollable Middle Body (Form & Cart Items) -->
            <div class="card-body p-3 overflow-y-auto flex-grow-1" id="pos-tabs-scroll-body" style="min-height: 0;">
                <div id="new-tab-form-container" class="card bg-light border-0 p-3 mb-3 rounded-4 d-none">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted mb-2">
                            Tab Type
                        </label>

                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="new-tab-type" id="type-walkin" value="walk_in" checked>
                            <label class="btn btn-outline-secondary py-2 px-3" for="type-walkin" style="font-size: 0.85rem;">
                                Walk-in
                            </label>

                            <input type="radio" class="btn-check" name="new-tab-type" id="type-room" value="room">
                            <label class="btn btn-outline-secondary py-2 px-3" for="type-room" style="font-size: 0.85rem;">
                                Room
                            </label>

                            <input type="radio" class="btn-check" name="new-tab-type" id="type-account" value="account">
                            <label class="btn btn-outline-warning text-dark py-2 px-3 fw-bold border-warning" for="type-account" style="font-size: 0.85rem; background: #fffdf2;">
                                <i class="fa-solid fa-crown text-warning me-1"></i> Account (VIP)
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

                    <!-- Credit Account input panel (hidden by default) -->
                    <div id="new-tab-account-panel" class="mb-2 d-none">
                        <select id="new-tab-account" class="form-select py-3 border-warning fw-medium" style="background-color: #fffdf5;">
                            <option value="">Select VIP credit account...</option>
                            @foreach($creditAccounts as $account)
                                <option value="{{ $account->account_id }}">👑 VIP — {{ $account->account_name }} (Limit: ₱{{ number_format($account->available_credit, 2) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" class="btn btn-primary w-100 py-2.5 fw-bold shadow-sm" id="open-tab-btn">
                        <i class="fa-solid fa-folder-open me-1"></i> Open Tab
                    </button>
                </div>

                <div id="active-tab-panel" class="d-none">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h5 id="active-tab-name" class="fw-bold text-dark mb-1" style="font-size: 1.25rem;"></h5>
                            <div id="active-tab-badge" class="mt-1"></div>
                            <div id="active-tab-discount-badge" class="mt-1 d-none text-success small fw-bold"></div>
                        </div>
                        <span class="badge bg-primary px-3 py-2 fs-6 shadow-sm" id="active-tab-total">₱0.00</span>
                    </div>

                    <!-- Alert message if there is a pending cancel request -->
                    <div id="active-tab-pending-alert" class="alert alert-warning py-2 px-3 small d-none my-2">
                        <i class="fa-solid fa-clock me-1"></i> Cancellation Pending Authorization
                    </div>

                    <!-- Cart items list (scrolls inside body) -->
                    <div id="cart-items" class="small mb-3"></div>

                    <div class="mt-3 mb-2">
                        <label class="fw-semibold text-muted mb-1 small">
                            <i class="fa-solid fa-note-sticky me-1"></i> Item Notes
                        </label>
                        <textarea
                            id="item-notes"
                            class="form-control form-control-sm"
                            rows="2"
                            placeholder="Less sugar, no ice, extra shot, etc.">
                        </textarea>
                    </div>
                </div>

                <div id="no-tab-message" class="text-muted text-center py-4">
                    <i class="fa-solid fa-hand-pointer fa-2x mb-2 opacity-50"></i>
                    <div>Select an existing customer tab or tap <strong>+ New Customer</strong> to open a new tab.</div>
                </div>
            </div>

            <!-- Floating Sticky Footer Actions Bar (Fixed at bottom of Customer Tabs module) -->
            <div id="pos-floating-footer" class="card-footer border-top bg-white p-3 shadow-lg flex-shrink-0" style="position: sticky; bottom: 0; z-index: 25; border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
                <div id="active-tab-footer-summary" class="d-none">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Subtotal</span>
                        <span id="cart-subtotal" class="text-muted">₱0.00</span>
                    </div>
                    <div class="d-flex justify-content-between small text-success fw-semibold mb-1">
                        <span>Discount</span>
                        <span id="cart-discount" class="text-success fw-semibold">-₱0.00</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-2 border-top pt-1">
                        <span class="text-dark">Total</span>
                        <span id="cart-total" class="text-primary">₱0.00</span>
                    </div>

                    <!-- Primary Checkout Action Buttons -->
                    <div id="checkout-actions-container" class="mb-2">
                        <!-- Walk-in actions -->
                        <div id="walkin-checkout-actions" class="d-none">
                            <button type="button" class="btn btn-success btn-lg w-100 fw-bold shadow-sm checkout-action-btn" id="pay-walkin-btn">
                                <i class="fa-solid fa-cash-register me-2"></i> Pay / Close Tab
                            </button>
                        </div>

                        <!-- Room charge actions -->
                        <div id="room-checkout-actions" class="d-none">
                            <button type="button" class="btn btn-success btn-lg w-100 mb-1.5 fw-bold shadow-sm checkout-action-btn" id="charge-room-btn">
                                <i class="fa-solid fa-user me-2"></i> Charge to Guest
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm w-100 checkout-action-btn" id="pay-direct-btn">
                                <i class="fa-solid fa-cash-register me-1"></i> Pay Directly
                            </button>
                        </div>

                        <!-- Account charge actions -->
                        <div id="account-checkout-actions" class="d-none">
                            <button type="button" class="btn btn-success btn-lg w-100 mb-1.5 fw-bold shadow-sm checkout-action-btn" id="charge-account-btn">
                                <i class="fa-solid fa-file-invoice-dollar me-2"></i> Charge to Account
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm w-100 checkout-action-btn" id="pay-direct-account-btn">
                                <i class="fa-solid fa-cash-register me-1"></i> Pay Directly
                            </button>
                        </div>
                    </div>

                    <!-- Floating Quick Management Actions (Transfer, Discount, Cancel Tab) -->
                    <div class="row g-1">
                        <div class="col-4">
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100 py-1.5 checkout-action-btn" id="transfer-tab-btn" title="Transfer Tab">
                                <i class="fa-solid fa-exchange-alt me-1"></i> Transfer
                            </button>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-outline-info btn-sm w-100 py-1.5 checkout-action-btn" id="discount-tab-btn" title="Discount Tab">
                                <i class="fa-solid fa-tags me-1"></i> Discount
                            </button>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-outline-danger btn-sm w-100 py-1.5 checkout-action-btn" id="cancel-tab-btn" title="Cancel Tab">
                                <i class="fa-solid fa-ban me-1"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <div id="no-tab-footer-summary" class="text-center text-muted small py-1">
                    <i class="fa-solid fa-circle-info me-1 text-primary"></i> Pick a tab or click <strong>+ New Customer</strong> above.
                </div>
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
                <h5 class="modal-title"><i class="fa-solid fa-user me-2"></i>Charge to Guest</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">Select the guest to charge this order:</p>
                <div class="mb-3">
                    <select id="charge-guest-select" class="form-select">
                        <option value="">Loading guests...</option>
                    </select>
                </div>
                <p id="room-charge-warning-text" class="text-muted small mb-0"></p>
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

<div class="modal fade" id="transferTabModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title"><i class="fa-solid fa-exchange-alt me-2"></i>Transfer Tab</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">Change the billing target for this tab.</p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">New Tab Type</label>
                    <select id="transfer-tab-type" class="form-select">
                        <option value="room">Room Charge</option>
                        <option value="account">Account Charge</option>
                    </select>
                </div>
                <div id="transfer-room-panel" class="mb-3 d-none">
                    <label class="form-label fw-semibold">Select Room</label>
                    <select id="transfer-guest" class="form-select">
                        <option value="">Select occupied room...</option>
                    </select>
                </div>
                <div id="transfer-account-panel" class="mb-3 d-none">
                    <label class="form-label fw-semibold">Select VIP Account</label>
                    <select id="transfer-account" class="form-select border-warning">
                        <option value="">Select VIP credit account...</option>
                        @foreach($creditAccounts as $account)
                            <option value="{{ $account->account_id }}">👑 VIP — {{ $account->account_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-secondary btn-sm" id="confirm-transfer-tab-btn">Transfer</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="discountTabModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-dark">
                <h5 class="modal-title"><i class="fa-solid fa-tags me-2"></i>Apply Discount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Discount Type</label>
                    <select id="discount-type" class="form-select">
                        <option value="Senior Citizen">Senior Citizen (20%)</option>
                        <option value="PWD">PWD (20%)</option>
                        <option value="Custom">Custom / Manager's Discount</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Amount</label>
                    <div class="input-group">
                        <input type="number" id="discount-amount" class="form-control" value="20" min="0" step="0.01">
                        <select id="discount-is-percentage" class="form-select" style="max-width: 100px;">
                            <option value="1">%</option>
                            <option value="0">₱</option>
                        </select>
                    </div>
                    <div id="discount-error-message" class="text-danger small mt-1 d-none">Discount amount cannot exceed the total amount.</div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light btn-sm" id="remove-discount-btn">Remove Discount</button>
                <button type="button" class="btn btn-info btn-sm" id="confirm-discount-btn">Apply Discount</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmAccountChargeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Charge to Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p id="account-charge-warning-text" class="text-muted small mb-0">Are you sure you want to charge the total amount to the selected corporate/VIP account?</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm text-white" id="confirm-account-charge-submit-btn">Confirm Charge</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-3">
                <div id="receipt-content"></div>
            </div>
            <div class="modal-footer border-0 p-2 d-flex justify-content-center">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="window.printReceipt()">
                    <i class="fa-solid fa-print me-1"></i> Print Receipt
                </button>
            </div>
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

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.pairing-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const rawItems = this.getAttribute('data-items');
            if (!rawItems) return;
            const firstItem = rawItems.split(',')[0].trim();
            const searchInput = document.getElementById('product-search');
            if (searchInput) {
                searchInput.value = firstItem;
                searchInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });
    });
});
</script>
<script src="{{ asset('js/coffeeshop/pos-terminal.js') }}"></script>
@endpush
