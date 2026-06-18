@extends('layouts.app')

@section('title', 'Sales Report')
@section('pageTitle', 'Sales & Revenue')
@section('pageSubtitle', 'Hotel coffee shop sales performance and reporting')

@section('content')

<!-- KPI ROW (REAL BUSINESS METRICS) -->
<div class="row g-3 mb-3">

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Sales</div>
                <div class="fw-bold fs-4">₱48,250</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Transactions</div>
                <div class="fw-bold fs-4">182</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Average Ticket</div>
                <div class="fw-bold fs-4">₱265</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Refunds</div>
                <div class="fw-bold fs-4 text-danger">₱1,200</div>
            </div>
        </div>
    </div>

</div>

<!-- ACTION BAR (FILTER + EXPORT - REAL SYSTEM STYLE) -->
<div class="card border-0 shadow-sm mb-3">

    <div class="card-body d-flex justify-content-between align-items-center">

        <!-- LEFT: FILTER BUTTON -->
        <div class="d-flex gap-2">

            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#filterBox">
                <i class="fa-solid fa-filter me-1"></i>
                Filters
            </button>

            <button class="btn btn-outline-dark btn-sm">
                <i class="fa-solid fa-download me-1"></i>
                Export
            </button>

        </div>

        <!-- RIGHT: QUICK INFO -->
        <div class="text-muted small">
            Updated: Today 10:45 AM
        </div>

    </div>

    <!-- FILTER PANEL (COLLAPSE) -->
    <div class="collapse border-top" id="filterBox">
        <div class="p-3 bg-light">

            <div class="row g-2">

                <div class="col-md-3">
                    <input type="date" class="form-control form-control-sm">
                </div>

                <div class="col-md-3">
                    <input type="date" class="form-control form-control-sm">
                </div>

                <div class="col-md-3">
                    <select class="form-select form-select-sm">
                        <option>All Payment Types</option>
                        <option>Cash</option>
                        <option>Card</option>
                        <option>Room Charge</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button class="btn btn-primary btn-sm w-100">
                        Apply Filter
                    </button>
                </div>

            </div>

        </div>
    </div>

</div>

<!-- SALES TABLE -->
<div class="card border-0 shadow-sm">

    <div class="card-header bg-white border-0">
        <div class="fw-bold">Transaction Records</div>
        <small class="text-muted">Detailed sales history</small>
    </div>

    <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">

            <thead class="table-light">
                <tr>
                    <th>Transaction ID</th>
                    <th>Time</th>
                    <th>Items</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td class="fw-semibold">TXN-10021</td>
                    <td>10:30 AM</td>
                    <td>Cappuccino, Latte</td>
                    <td>Cash</td>
                    <td><span class="badge bg-success">Paid</span></td>
                    <td class="text-end fw-bold">₱420</td>
                </tr>

                <tr>
                    <td class="fw-semibold">TXN-10022</td>
                    <td>10:45 AM</td>
                    <td>Cheesecake</td>
                    <td>Card</td>
                    <td><span class="badge bg-success">Paid</span></td>
                    <td class="text-end fw-bold">₱180</td>
                </tr>

                <tr>
                    <td class="fw-semibold">TXN-10023</td>
                    <td>11:10 AM</td>
                    <td>Iced Americano</td>
                    <td>Room Charge</td>
                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                    <td class="text-end fw-bold">₱110</td>
                </tr>

            </tbody>

        </table>

    </div>

</div>

@endsection