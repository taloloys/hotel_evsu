@extends('layouts.app')

@section('title', 'Activity Logs')
@section('pageTitle', 'Activity Logs')
@section('pageSubtitle', 'System audit trail and user activity monitoring')

@section('content')

<!-- SUMMARY CARDS -->
<div class="row g-3 mb-3">

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Logs</div>
                    <div class="fw-bold fs-5">128</div>
                </div>
                <i class="fa-solid fa-clock-rotate-left text-primary fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Today</div>
                    <div class="fw-bold fs-5">24</div>
                </div>
                <i class="fa-solid fa-calendar-day text-success fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">System Alerts</div>
                    <div class="fw-bold fs-5">3</div>
                </div>
                <i class="fa-solid fa-triangle-exclamation text-danger fs-4"></i>
            </div>
        </div>
    </div>

</div>

<!-- TABLE CARD -->
<div class="card border-0 shadow-sm">

    <!-- HEADER (LEFT TITLE + RIGHT ACTIONS FIXED) -->
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">

        <!-- LEFT SIDE -->
        <div>
            <div class="fw-semibold">Recent Activity</div>
            <small class="text-muted">Latest system events</small>
        </div>

        <!-- RIGHT SIDE (FILTER + EXPORT) -->
        <div class="d-flex gap-2">

            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#filterBox">
                <i class="fa-solid fa-filter me-1"></i>
                Filter
            </button>

            <button class="btn btn-outline-dark btn-sm">
                <i class="fa-solid fa-download me-1"></i>
                Export
            </button>

        </div>

    </div>

    <!-- FILTER PANEL -->
    <div class="collapse border-top" id="filterBox">
        <div class="p-3 bg-light">

            <div class="row g-2">

                <div class="col-md-4">
                    <select class="form-select form-select-sm">
                        <option>User</option>
                        <option>Admin</option>
                        <option>Front Desk</option>
                        <option>Cashier</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <select class="form-select form-select-sm">
                        <option>Module</option>
                        <option>Users</option>
                        <option>Reservations</option>
                        <option>Accounting</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <select class="form-select form-select-sm">
                        <option>Status</option>
                        <option>Success</option>
                        <option>Pending</option>
                        <option>Failed</option>
                    </select>
                </div>

            </div>

        </div>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">

            <thead class="table-light">
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Module</th>
                    <th>Timestamp</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td class="fw-semibold">Admin</td>
                    <td>Created new user account</td>
                    <td>Users</td>
                    <td class="text-muted">Just now</td>
                    <td><span class="badge bg-success">Success</span></td>
                </tr>

                <tr>
                    <td class="fw-semibold">Front Desk</td>
                    <td>Checked-in guest</td>
                    <td>Reservations</td>
                    <td class="text-muted">5 mins ago</td>
                    <td><span class="badge bg-success">Success</span></td>
                </tr>

                <tr>
                    <td class="fw-semibold">Cashier</td>
                    <td>Processed payment</td>
                    <td>Accounting</td>
                    <td class="text-muted">20 mins ago</td>
                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                </tr>

            </tbody>

        </table>

    </div>

</div>

@endsection