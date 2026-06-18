@extends('layouts.app')

@section('title', 'Accounting Dashboard')
@section('pageTitle', 'Finance Overview')
@section('pageSubtitle', 'Hotel financial performance at a glance')

@section('content')

<!-- KPI ROW (MINIMAL - ONLY CORE METRICS) -->
<div class="row g-3 mb-4">

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Revenue</div>
                <div class="fw-bold fs-4">₱120,450</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Profit</div>
                <div class="fw-bold fs-4 text-primary">₱86,320</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Receivables</div>
                <div class="fw-bold fs-4 text-warning">₱12,800</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Expenses</div>
                <div class="fw-bold fs-4 text-danger">₱34,120</div>
            </div>
        </div>
    </div>

</div>

<!-- CASH SUMMARY (SINGLE CLEAN STRIP) -->
<div class="card border-0 shadow-sm mb-4">

    <div class="card-body py-3">

        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-bold">Cash Summary</div>
            <small class="text-muted">Today</small>
        </div>

        <div class="d-flex justify-content-between">

            <div>
                <div class="text-muted small">Cash In</div>
                <div class="fw-bold text-success">₱45,200</div>
            </div>

            <div>
                <div class="text-muted small">Cash Out</div>
                <div class="fw-bold text-danger">₱30,500</div>
            </div>

            <div>
                <div class="text-muted small">Net Flow</div>
                <div class="fw-bold text-primary">₱14,700</div>
            </div>

        </div>

    </div>

</div>

<!-- MAIN TABLE (PRIMARY FOCUS) -->
<div class="card border-0 shadow-sm">

    <div class="card-body">

        <div class="mb-3">
            <div class="fw-bold">Recent Transactions</div>
            <small class="text-muted">Latest financial activity across hotel operations</small>
        </div>

        <div class="table-responsive">

            <table class="table align-middle mb-0">

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
                        <td>AC-1001</td>
                        <td><span class="badge bg-success">Income</span></td>
                        <td>Room Charge Payment</td>
                        <td><span class="badge bg-success">Posted</span></td>
                        <td class="text-end fw-bold">₱2,500</td>
                    </tr>

                    <tr>
                        <td>AC-1002</td>
                        <td><span class="badge bg-danger">Expense</span></td>
                        <td>Cleaning Supplies</td>
                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                        <td class="text-end fw-bold text-danger">₱1,200</td>
                    </tr>

                    <tr>
                        <td>AC-1003</td>
                        <td><span class="badge bg-success">Income</span></td>
                        <td>Coffee Shop Sales</td>
                        <td><span class="badge bg-success">Posted</span></td>
                        <td class="text-end fw-bold">₱860</td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection