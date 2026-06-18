@extends('layouts.app')

@section('title', 'Coffee Shop POS')
@section('pageTitle', 'Coffee Shop POS Terminal')
@section('pageSubtitle', 'Fast billing system for hotel food & beverage operations')

@section('content')

<div class="row g-3">

    <!-- LEFT: PRODUCT CATEGORIES + ITEMS -->
    <div class="col-lg-8">

        <!-- TOP BAR (SEARCH + FILTER LIKE REAL POS) -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">

                <!-- LEFT: CATEGORY QUICK BUTTONS -->
                <div class="d-flex gap-2 flex-wrap">

                    <button class="btn btn-dark btn-sm">All</button>
                    <button class="btn btn-outline-secondary btn-sm">Coffee</button>
                    <button class="btn btn-outline-secondary btn-sm">Tea</button>
                    <button class="btn btn-outline-secondary btn-sm">Food</button>
                    <button class="btn btn-outline-secondary btn-sm">Dessert</button>

                </div>

                <!-- RIGHT: SEARCH + FILTER -->
                <div class="d-flex gap-2 align-items-center">

                    <div class="input-group" style="width: 240px;">
                        <span class="input-group-text bg-white">
                            <i class="fa-solid fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control form-control-sm" placeholder="Search item...">
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-filter"></i>
                        </button>

                        <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 220px;">

                            <label class="form-label small">Availability</label>
                            <select class="form-select form-select-sm mb-2">
                                <option>All</option>
                                <option>Available</option>
                                <option>Unavailable</option>
                            </select>

                            <label class="form-label small">Price Range</label>
                            <select class="form-select form-select-sm mb-3">
                                <option>All</option>
                                <option>Low to High</option>
                                <option>High to Low</option>
                            </select>

                            <button class="btn btn-primary btn-sm w-100">Apply</button>

                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- PRODUCT GRID (REAL POS STYLE TILE GRID) -->
        <div class="row g-3">

            <!-- ITEM -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 product-card">
                    <div class="card-body text-center">

                        <i class="fa-solid fa-mug-hot fa-2x text-warning mb-2"></i>

                        <div class="fw-semibold">Americano</div>
                        <small class="text-muted">Coffee</small>

                        <div class="fw-bold text-primary mt-2">₱120</div>

                        <button class="btn btn-primary btn-sm w-100 mt-2">
                            ADD
                        </button>

                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 product-card">
                    <div class="card-body text-center">

                        <i class="fa-solid fa-mug-saucer fa-2x text-info mb-2"></i>

                        <div class="fw-semibold">Latte</div>
                        <small class="text-muted">Milk Coffee</small>

                        <div class="fw-bold text-primary mt-2">₱150</div>

                        <button class="btn btn-primary btn-sm w-100 mt-2">
                            ADD
                        </button>

                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 product-card">
                    <div class="card-body text-center">

                        <i class="fa-solid fa-cookie-bite fa-2x text-danger mb-2"></i>

                        <div class="fw-semibold">Cookies</div>
                        <small class="text-muted">Dessert</small>

                        <div class="fw-bold text-primary mt-2">₱90</div>

                        <button class="btn btn-primary btn-sm w-100 mt-2">
                            ADD
                        </button>

                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- RIGHT: CASHIER PANEL (REAL HOTEL POS STYLE) -->
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm sticky-top" style="top: 15px;">

            <!-- HEADER -->
            <div class="card-header bg-white">
                <div class="fw-bold">🧾 Order Panel</div>
                <small class="text-muted">Room / Walk-in / Take-out</small>
            </div>

            <div class="card-body">

                <!-- ORDER TYPE -->
                <div class="mb-2">
                    <label class="form-label small">Order Type</label>
                    <select class="form-select form-select-sm">
                        <option>Walk-in</option>
                        <option>Room Charge</option>
                        <option>Take-out</option>
                    </select>
                </div>

                <!-- ROOM (HOTEL FEATURE) -->
                <div class="mb-3">
                    <label class="form-label small">Room No (if charge to room)</label>
                    <input type="text" class="form-control form-control-sm" placeholder="e.g. 101">
                </div>

                <hr>

                <!-- CART ITEMS -->
                <div class="small">

                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <div class="fw-semibold">Latte</div>
                            <small class="text-muted">x1</small>
                        </div>
                        <div>₱150</div>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <div class="fw-semibold">Americano</div>
                            <small class="text-muted">x2</small>
                        </div>
                        <div>₱240</div>
                    </div>

                </div>

                <hr>

                <!-- TOTAL -->
                <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                    <span>Total</span>
                    <span>₱390</span>
                </div>

                <!-- PAYMENT -->
                <div class="mb-2">
                    <label class="form-label small">Cash</label>
                    <input type="number" class="form-control form-control-sm">
                </div>

                <div class="mb-3">
                    <label class="form-label small">Change</label>
                    <input type="text" class="form-control form-control-sm" readonly>
                </div>

                <!-- ACTION BUTTONS -->
                <button class="btn btn-success w-100 mb-2">
                    <i class="fa-solid fa-cash-register me-1"></i>
                    Checkout
                </button>

                <button class="btn btn-outline-danger w-100">
                    Void / Clear
                </button>

            </div>

        </div>

    </div>

</div>

@endsection