@extends('layouts.app')

@section('title', 'Sales Report')
@section('pageTitle', 'Sales & Revenue')
@section('pageSubtitle', 'Hotel coffee shop sales performance and reporting')

@section('content')

<div class="coffeeshop-page-shell">
    <div class="coffeeshop-hero">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
            <div>
                <div class="fw-bold fs-5">Sales and revenue</div>
                <div class="opacity-75 mt-1">Review overall performance with the same polished coffee-shop experience.</div>
            </div>
        </div>
    </div>

    <div class="coffeeshop-panel p-3 p-lg-4">

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
<div class="coffeeshop-card border-0 shadow-sm mb-3">

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
                    <button class="btn text-white btn-sm w-100 fw-bold" style="background-color: #334c42; border: none; font-family: 'Lucida Fax', serif;">
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
        <div class="fw-bold font-display" style="color: #504538;">Transaction Records</div>
        <small style="color: #827567; font-family: 'Lucida Fax', serif;">Detailed sales history</small>
    </div>

    <div class="table-responsive">

        <table class="table table-hover align-middle mb-0 coffeeshop-table">

            <thead style="background: #f8f3ed; color: #827567; font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">
                <tr>
                    <th style="color: #827567;">Transaction ID</th>
                    <th style="color: #827567;">Time</th>
                    <th style="color: #827567;">Items</th>
                    <th style="color: #827567;">Payment</th>
                    <th style="color: #827567;">Status</th>
                    <th class="text-end" style="color: #827567;">Total</th>
                </tr>
            </thead>

            <tbody style="font-family: 'Lucida Fax', 'Georgia', serif;">

                <tr>
                    <td class="fw-semibold" style="color: #504538;">TXN-10021</td>
                    <td style="color: #212529;">10:30 AM</td>
                    <td style="color: #212529;">Cappuccino, Latte</td>
                    <td style="color: #212529;">Cash</td>
                    <td><span class="badge bg-success">Paid</span></td>
                    <td class="text-end fw-bold" style="color: #334c42;">₱420</td>
                </tr>

                <tr>
                    <td class="fw-semibold" style="color: #504538;">TXN-10022</td>
                    <td style="color: #212529;">10:45 AM</td>
                    <td style="color: #212529;">Cheesecake</td>
                    <td style="color: #212529;">Card</td>
                    <td><span class="badge bg-success">Paid</span></td>
                    <td class="text-end fw-bold" style="color: #334c42;">₱180</td>
                </tr>

                <tr>
                    <td class="fw-semibold" style="color: #504538;">TXN-10023</td>
                    <td style="color: #212529;">11:10 AM</td>
                    <td style="color: #212529;">Iced Americano</td>
                    <td style="color: #212529;">Room Charge</td>
                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                    <td class="text-end fw-bold" style="color: #334c42;">₱110</td>
                </tr>

            </tbody>

        </table>

    </div>

    </div>
</div>

@endsection