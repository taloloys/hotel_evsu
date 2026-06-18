@extends('layouts.app')

@section('title', 'Dashboard')
@section('pageTitle', 'Coffee Shop Dashboard')
@section('pageSubtitle', 'Coffee shop operations overview')

@section('content')

<style>
    .coffee-dashboard {
        background: #f5f5f5;
        border-radius: 20px;
        padding: 20px;
    }

    .product-type-btn {
        width: 100%;
        border: none;
        background: #ffffff;
        padding: 14px;
        border-radius: 12px;
        margin-bottom: 10px;
        font-weight: 500;
        transition: .3s;
        text-align: left;
    }

    .product-type-btn.active {
        background: #6f4e37;
        color: white;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
        gap: 18px;
    }

    .product-box {
        height: 85px;
        border-radius: 14px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 22px;
        cursor: pointer;
        transition: .3s;
        box-shadow: 0 2px 10px rgba(0,0,0,.08);
        position: relative;
        background: white;
    }

    .product-box:hover {
        transform: translateY(-3px);
    }

    .available {
        background: #d1fae5;
        color: #065f46;
    }

    .low-stock {
        background: #fef3c7;
        color: #92400e;
    }

    .out-stock {
        background: #fee2e2;
        color: #991b1b;
    }

    .item-number {
        position: absolute;
        bottom: 5px;
        font-size: 11px;
        font-weight: 600;
    }

    .product-wrapper {
        position: relative;
    }
</style>

<!-- KPI CARDS -->
<div class="row g-4 mb-4">

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted">Today's Sales</small>
                        <h2 class="fw-bold mt-2">₱ 5,200</h2>
                    </div>
                    <i class="fa-solid fa-coins fa-2x text-warning"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted">Orders</small>
                        <h2 class="fw-bold mt-2">48</h2>
                    </div>
                    <i class="fa-solid fa-receipt fa-2x text-primary"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted">Products</small>
                        <h2 class="fw-bold mt-2">24</h2>
                    </div>
                    <i class="fa-solid fa-mug-hot fa-2x text-danger"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted">Low Stock Items</small>
                        <h2 class="fw-bold mt-2">5</h2>
                    </div>
                    <i class="fa-solid fa-triangle-exclamation fa-2x text-warning"></i>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- MAIN SECTION -->
<div class="card border-0 shadow-sm">

    <div class="card-header bg-white">
        <h5 class="fw-bold mb-0">
            Coffee Shop Inventory Monitoring
        </h5>
    </div>

    <div class="card-body coffee-dashboard">

        <!-- LEGEND -->
        <div class="d-flex flex-wrap gap-4 mb-4">

            <div>
                <span class="legend-dot bg-success"></span>
                Available
            </div>

            <div>
                <span class="legend-dot bg-warning"></span>
                Low Stock
            </div>

            <div>
                <span class="legend-dot bg-danger"></span>
                Out of Stock
            </div>

        </div>

        <div class="row">

            <!-- LEFT CATEGORY MENU -->
            <div class="col-lg-2 mb-3">

                <button class="product-type-btn active">
                    Coffee
                </button>

                <button class="product-type-btn">
                    Milk Tea
                </button>

                <button class="product-type-btn">
                    Frappes
                </button>

                <button class="product-type-btn">
                    Pastries
                </button>

                <button class="product-type-btn">
                    Snacks
                </button>

            </div>

            <!-- PRODUCT GRID -->
            <div class="col-lg-10">

                <div class="product-grid">

                    <div class="product-wrapper">
                        <div class="product-box available">
                            <i class="fa-solid fa-mug-hot"></i>
                            <div class="item-number">Latte</div>
                        </div>
                    </div>

                    <div class="product-wrapper">
                        <div class="product-box low-stock">
                            <i class="fa-solid fa-mug-saucer"></i>
                            <div class="item-number">Mocha</div>
                        </div>
                    </div>

                    <div class="product-wrapper">
                        <div class="product-box available">
                            <i class="fa-solid fa-ice-cream"></i>
                            <div class="item-number">Frappe</div>
                        </div>
                    </div>

                    <div class="product-wrapper">
                        <div class="product-box out-stock">
                            <i class="fa-solid fa-cookie"></i>
                            <div class="item-number">Cookie</div>
                        </div>
                    </div>

                    <div class="product-wrapper">
                        <div class="product-box available">
                            <i class="fa-solid fa-bread-slice"></i>
                            <div class="item-number">Bread</div>
                        </div>
                    </div>

                    <div class="product-wrapper">
                        <div class="product-box low-stock">
                            <i class="fa-solid fa-bottle-water"></i>
                            <div class="item-number">Milk</div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection