@extends('layouts.app')

@section('title', 'Receivables')
@section('pageTitle', 'Accounts Receivable')
@section('pageSubtitle', 'Monitor outstanding guest balances and pending settlements')

@section('content')

<!-- KPI ROW -->
<div class="row g-3 mb-4">

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Receivables</div>
                <div class="fw-bold fs-3">₱68,450</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">0–30 Days</div>
                <div class="fw-bold fs-3 text-success">₱32,200</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">31–60 Days</div>
                <div class="fw-bold fs-3 text-warning">₱18,150</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Overdue</div>
                <div class="fw-bold fs-3 text-danger">₱18,100</div>
            </div>
        </div>
    </div>

</div>


<!-- ACTION BAR -->
<div class="card border-0 shadow-sm mb-3">

    <div class="card-body d-flex justify-content-between align-items-center">

        <div>
            <div class="fw-bold">Outstanding Accounts</div>
            <small class="text-muted">Guest balances requiring settlement</small>
        </div>

        <div class="d-flex gap-2">

            <!-- SEARCH -->
            <input type="text"
                   class="form-control form-control-sm"
                   style="width: 220px;"
                   placeholder="Search guest / room">

            <!-- FILTER -->
            <select class="form-select form-select-sm" style="width: 160px;">
                <option>All Status</option>
                <option>Current</option>
                <option>Overdue</option>
                <option>Critical</option>
            </select>

            <!-- EXPORT -->
            <button class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-download me-1"></i>
                Export
            </button>

        </div>

    </div>

</div>


<!-- TABLE -->
<div class="card border-0 shadow-sm">

    <div class="table-responsive">

        <table class="table align-middle mb-0">

            <thead class="table-light">
                <tr>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Invoice</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th class="text-end">Balance</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>Juan Dela Cruz</td>
                    <td>Room 101</td>
                    <td>INV-1001</td>
                    <td>2026-06-25</td>
                    <td><span class="badge bg-success">Current</span></td>
                    <td class="text-end fw-bold">₱12,500</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                </tr>

                <tr>
                    <td>Maria Santos</td>
                    <td>Room 205</td>
                    <td>INV-1002</td>
                    <td>2026-06-20</td>
                    <td><span class="badge bg-warning text-dark">Overdue</span></td>
                    <td class="text-end fw-bold text-warning">₱18,150</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                </tr>

                <tr>
                    <td>John Smith</td>
                    <td>Room 310</td>
                    <td>INV-1003</td>
                    <td>2026-06-15</td>
                    <td><span class="badge bg-danger">Critical</span></td>
                    <td class="text-end fw-bold text-danger">₱18,100</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                </tr>

            </tbody>

        </table>

    </div>

</div>

@endsection