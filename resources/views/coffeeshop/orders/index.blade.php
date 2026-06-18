@extends('layouts.app')

@section('title', 'POS Orders')
@section('pageTitle', 'Coffee Shop POS')
@section('pageSubtitle', 'Create and manage customer orders in real time')

@section('content')

<div class="row g-3">

    <!-- LEFT SIDE: MENU / PRODUCTS -->
    <div class="col-lg-8">

        <!-- TOP BAR -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">

                <!-- CATEGORY FILTER -->
                <div class="d-flex gap-2 flex-wrap">

                    <button class="btn btn-sm btn-primary">All</button>
                    <button class="btn btn-sm btn-outline-secondary">Coffee</button>
                    <button class="btn btn-sm btn-outline-secondary">Tea</button>
                    <button class="btn btn-sm btn-outline-secondary">Pastry</button>
                    <button class="btn btn-sm btn-outline-secondary">Food</button>

                </div>

                <!-- SEARCH -->
                <div style="width: 250px;">
                    <input type="text" class="form-control form-control-sm"
                           placeholder="Search menu item...">
                </div>

            </div>
        </div>

        <!-- PRODUCT GRID -->
        <div class="row g-3">

            <!-- PRODUCT CARD -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">

                        <div class="bg-light rounded mb-2" style="height:90px;"></div>

                        <div class="fw-semibold">Cappuccino</div>
                        <div class="text-muted small">Hot Coffee</div>

                        <div class="fw-bold mt-2">₱120</div>

                        <button class="btn btn-primary btn-sm mt-2 w-100">
                            Add to Order
                        </button>

                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">

                        <div class="bg-light rounded mb-2" style="height:90px;"></div>

                        <div class="fw-semibold">Iced Latte</div>
                        <div class="text-muted small">Cold Coffee</div>

                        <div class="fw-bold mt-2">₱140</div>

                        <button class="btn btn-primary btn-sm mt-2 w-100">
                            Add to Order
                        </button>

                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">

                        <div class="bg-light rounded mb-2" style="height:90px;"></div>

                        <div class="fw-semibold">Chocolate Cake</div>
                        <div class="text-muted small">Dessert</div>

                        <div class="fw-bold mt-2">₱180</div>

                        <button class="btn btn-primary btn-sm mt-2 w-100">
                            Add to Order
                        </button>

                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- RIGHT SIDE: CART -->
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm sticky-top" style="top:20px;">

            <!-- CART HEADER -->
            <div class="card-header bg-white border-0">
                <div class="fw-bold">Current Order</div>
                <small class="text-muted">Table / Walk-in Customer</small>
            </div>

            <!-- CART ITEMS -->
            <div class="card-body p-2">

                <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">

                    <div>
                        <div class="fw-semibold">Cappuccino</div>
                        <small class="text-muted">₱120 x 1</small>
                    </div>

                    <div class="text-end">
                        <div class="fw-bold">₱120</div>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>

                </div>

                <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">

                    <div>
                        <div class="fw-semibold">Chocolate Cake</div>
                        <small class="text-muted">₱180 x 2</small>
                    </div>

                    <div class="text-end">
                        <div class="fw-bold">₱360</div>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>

                </div>

            </div>

            <!-- TOTAL -->
            <div class="card-footer bg-white">

                <div class="d-flex justify-content-between">
                    <span>Subtotal</span>
                    <span>₱480</span>
                </div>

                <div class="d-flex justify-content-between">
                    <span>Tax (12%)</span>
                    <span>₱57.60</span>
                </div>

                <hr>

                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total</span>
                    <span>₱537.60</span>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="d-grid gap-2 mt-3">

                    <button class="btn btn-success">
                        <i class="fa-solid fa-cash-register me-1"></i>
                        Checkout
                    </button>

                    <button class="btn btn-outline-warning">
                        Hold Order
                    </button>

                    <button class="btn btn-outline-danger">
                        Cancel
                    </button>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection