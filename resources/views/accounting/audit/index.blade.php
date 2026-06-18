@extends('layouts.app')

@section('title', 'Audit Logs')
@section('pageTitle', 'System Audit Trail')
@section('pageSubtitle', 'Track all system activities, user actions, and security events')

@section('content')

<!-- FILTER PANEL (SEPARATE FROM ACTIONS) -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">

        <div class="row g-3 align-items-end">

            <!-- USER FILTER -->
            <div class="col-md-4">
                <label class="form-label small text-muted">User</label>
                <select class="form-select form-select-sm">
                    <option>All Users</option>
                    <option>Admin</option>
                    <option>Front Desk</option>
                    <option>Accounting</option>
                    <option>System</option>
                </select>
            </div>

            <!-- ACTION FILTER -->
            <div class="col-md-4">
                <label class="form-label small text-muted">Action Type</label>
                <select class="form-select form-select-sm">
                    <option>All Actions</option>
                    <option>Create</option>
                    <option>Update</option>
                    <option>Delete</option>
                    <option>Login</option>
                    <option>Logout</option>
                </select>
            </div>

            <!-- MODULE FILTER -->
            <div class="col-md-4">
                <label class="form-label small text-muted">Module</label>
                <select class="form-select form-select-sm">
                    <option>All Modules</option>
                    <option>Rooms</option>
                    <option>Billing</option>
                    <option>Payments</option>
                    <option>Users</option>
                    <option>System</option>
                </select>
            </div>

        </div>

    </div>
</div>

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-3">

    <div>
        <h5 class="fw-bold mb-0">Audit Logs</h5>
        <small class="text-muted">Complete system activity tracking</small>
    </div>

</div>

<!-- QUICK ACTIONS (NO BACKGROUND CARD) -->
<div class="mb-3">

    <div class="d-flex justify-content-end align-items-center gap-2">

        <!-- SEARCH -->
        <div class="input-group input-group-sm" style="width: 280px;">
            <span class="input-group-text bg-transparent border-end-0">
                <i class="fa-solid fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control border-start-0" placeholder="Search logs...">
        </div>

        <!-- EXPORT -->
        <button class="btn btn-outline-dark btn-sm">
            <i class="fa-solid fa-download me-1"></i>
            Export
        </button>

    </div>

</div>

<!-- LOG TABLE -->
<div class="card border-0 shadow-sm">

    <div class="table-responsive">

        <table class="table align-middle mb-0">

            <thead class="table-light">
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Module</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th class="text-end">IP Address</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td class="text-muted">2026-06-18 10:32</td>
                    <td>
                        <div class="fw-semibold">Admin</div>
                        <small class="text-muted">System Admin</small>
                    </td>
                    <td><span class="badge bg-primary">UPDATE</span></td>
                    <td>Rooms</td>
                    <td>Updated room rate for Room 205</td>
                    <td><span class="badge bg-success">Success</span></td>
                    <td class="text-end text-muted">192.168.1.10</td>
                </tr>

                <tr>
                    <td class="text-muted">2026-06-18 09:10</td>
                    <td>
                        <div class="fw-semibold">Front Desk</div>
                        <small class="text-muted">Reception</small>
                    </td>
                    <td><span class="badge bg-success">LOGIN</span></td>
                    <td>Auth</td>
                    <td>User logged into system</td>
                    <td><span class="badge bg-success">Success</span></td>
                    <td class="text-end text-muted">192.168.1.22</td>
                </tr>

                <tr>
                    <td class="text-muted">2026-06-18 08:45</td>
                    <td>
                        <div class="fw-semibold">Accounting</div>
                        <small class="text-muted">Finance Dept</small>
                    </td>
                    <td><span class="badge bg-danger">DELETE</span></td>
                    <td>Billing</td>
                    <td>Removed duplicate invoice INV-1021</td>
                    <td><span class="badge bg-warning text-dark">Reviewed</span></td>
                    <td class="text-end text-muted">192.168.1.15</td>
                </tr>

            </tbody>

        </table>

    </div>

</div>

@endsection