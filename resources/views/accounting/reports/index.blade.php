@extends('layouts.app')

@section('title', 'Accounting Reports')
@section('pageTitle', 'Financial Reports')
@section('pageSubtitle', 'Profit & Loss, Cash Flow, Revenue & Transactions')

@section('content')

<!-- TOP CONTROL BAR (CLEAN FILTER AREA - HOTEL STYLE) -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">

        <div class="row g-3 align-items-end">

            <!-- DATE FROM -->
            <div class="col-md-4">
                <label class="form-label small text-muted">Date From</label>
                <input type="date" class="form-control form-control-sm">
            </div>

            <!-- DATE TO -->
            <div class="col-md-4">
                <label class="form-label small text-muted">Date To</label>
                <input type="date" class="form-control form-control-sm">
            </div>

            <!-- REPORT TYPE -->
            <div class="col-md-4">
                <label class="form-label small text-muted">Report Type</label>
                <select class="form-select form-select-sm">
                    <option>All Reports</option>
                    <option>Profit & Loss</option>
                    <option>Cash Flow</option>
                    <option>Revenue</option>
                    <option>Transactions</option>
                </select>
            </div>

        </div>

    </div>
</div>

<!-- HEADER ACTIONS -->
<div class="d-flex justify-content-between align-items-center mb-3">

    <!-- LEFT -->
    <div>
        <h5 class="fw-bold mb-0">Financial Reports</h5>
        <small class="text-muted">Enterprise hotel financial overview</small>
    </div>

    <!-- RIGHT ACTIONS -->
    <div class="d-flex align-items-center gap-2">

        <!-- SEARCH -->
        <div class="input-group input-group-sm" style="width: 240px;">
            <span class="input-group-text bg-white border-end-0">
                <i class="fa-solid fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control border-start-0" placeholder="Search reports...">
        </div>

        <!-- EXPORT -->
        <button class="btn btn-outline-dark btn-sm px-3">
            <i class="fa-solid fa-download me-1"></i>
            Export
        </button>

    </div>

</div>

<!-- TABS (CLEAN STYLE) -->
<ul class="nav nav-tabs mb-3">

    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pl">
            Profit & Loss
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cash">
            Cash Flow
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#rev">
            Revenue
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tx">
            Transactions
        </button>
    </li>

</ul>

<!-- TAB CONTENT -->
<div class="tab-content">

    <!-- PROFIT & LOSS -->
    <div class="tab-pane fade show active" id="pl">
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="fw-bold mb-3">Profit & Loss Statement</div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">Total Revenue</span>
                    <span class="fw-bold text-success">₱120,450</span>
                </div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">Total Expenses</span>
                    <span class="fw-bold text-danger">₱34,120</span>
                </div>

                <div class="d-flex justify-content-between pt-3 fw-bold fs-5">
                    <span>Net Profit</span>
                    <span class="text-primary">₱86,330</span>
                </div>

            </div>
        </div>
    </div>

    <!-- CASH FLOW -->
    <div class="tab-pane fade" id="cash">
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="fw-bold mb-3">Cash Flow</div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">Cash In</span>
                    <span class="fw-bold">₱82,000</span>
                </div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">Cash Out</span>
                    <span class="fw-bold text-danger">₱30,500</span>
                </div>

                <div class="d-flex justify-content-between pt-3 fw-bold fs-5">
                    <span>Net Flow</span>
                    <span class="text-primary">₱51,500</span>
                </div>

            </div>
        </div>
    </div>

    <!-- REVENUE -->
    <div class="tab-pane fade" id="rev">
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="fw-bold mb-3">Revenue Breakdown</div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">Rooms</span>
                    <span class="fw-bold">₱90,200</span>
                </div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">Restaurant</span>
                    <span class="fw-bold">₱25,300</span>
                </div>

                <div class="d-flex justify-content-between pt-3 fw-bold fs-5">
                    <span>Total</span>
                    <span class="text-success">₱120,450</span>
                </div>

            </div>
        </div>
    </div>

    <!-- TRANSACTIONS -->
    <div class="tab-pane fade" id="tx">

        <div class="card border-0 shadow-sm">

            <div class="table-responsive">
                <table class="table mb-0 align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>Ref</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>TX-001</td>
                            <td>Income</td>
                            <td>Room Payment</td>
                            <td><span class="badge bg-success">Posted</span></td>
                            <td class="text-end fw-bold">₱2,500</td>
                        </tr>

                        <tr>
                            <td>TX-002</td>
                            <td>Expense</td>
                            <td>Supplies</td>
                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                            <td class="text-end fw-bold text-danger">₱1,200</td>
                        </tr>

                    </tbody>

                </table>
            </div>

        </div>

    </div>

</div>

@endsection