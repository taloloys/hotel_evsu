@extends('layouts.app')

@section('title', 'RBAC Management')
@section('pageTitle', 'Role & Permission Control')
@section('pageSubtitle', 'Manage system access for hotel staff')

@section('content')

<!-- TOP SUMMARY -->
<div class="row g-3 mb-4">

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Roles</div>
                    <h4 class="mb-0">4</h4>
                </div>
                <i class="fa-solid fa-user-shield fs-3 text-primary"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Permissions</div>
                    <h4 class="mb-0">5</h4>
                </div>
                <i class="fa-solid fa-key fs-3 text-warning"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">System Users</div>
                    <h4 class="mb-0">3</h4>
                </div>
                <i class="fa-solid fa-users fs-3 text-success"></i>
            </div>
        </div>
    </div>

</div>

<!-- MAIN CARDS -->
<div class="row g-4">

    <!-- ROLES -->
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0">
                <h6 class="fw-bold mb-0">Roles</h6>
            </div>

            <div class="card-body">

                <div class="list-group list-group-flush">

                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        Admin
                        <span class="badge bg-dark">Full Access</span>
                    </div>

                    <div class="list-group-item px-0">Front Desk</div>
                    <div class="list-group-item px-0">Accounting</div>
                    <div class="list-group-item px-0">Coffee Shop</div>

                </div>

            </div>

            <div class="card-footer bg-white border-0">
                <button class="btn btn-primary w-100">
                    <i class="fa-solid fa-plus me-2"></i>
                    Add Role
                </button>
            </div>

        </div>

    </div>

    <!-- PERMISSIONS -->
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0">
                <h6 class="fw-bold mb-0">Permissions</h6>
            </div>

            <div class="card-body">

                <div class="list-group list-group-flush">

                    <div class="list-group-item px-0">manage-users</div>
                    <div class="list-group-item px-0">manage-reservations</div>
                    <div class="list-group-item px-0">view-folio</div>
                    <div class="list-group-item px-0">process-checkout</div>
                    <div class="list-group-item px-0">manage-inventory</div>

                </div>

            </div>

            <div class="card-footer bg-white border-0">
                <button class="btn btn-warning w-100">
                    <i class="fa-solid fa-plus me-2"></i>
                    Add Permission
                </button>
            </div>

        </div>

    </div>

    <!-- USERS -->
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-0">
                <h6 class="fw-bold mb-0">Users</h6>
            </div>

            <div class="card-body">

                <div class="list-group list-group-flush">

                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        Front Desk User
                        <span class="badge bg-primary">Front Desk</span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        Cashier
                        <span class="badge bg-info">Accounting</span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        Admin User
                        <span class="badge bg-dark">Admin</span>
                    </div>

                </div>

            </div>

            <div class="card-footer bg-white border-0">
                <button class="btn btn-success w-100">
                    <i class="fa-solid fa-user-plus me-2"></i>
                    Assign Role
                </button>
            </div>

        </div>

    </div>

</div>

<!-- MATRIX -->
<div class="card border-0 shadow-sm mt-4">

    <div class="card-header bg-white border-0">
        <h6 class="fw-bold mb-0">Role Permission Matrix</h6>
    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">
                    <tr>
                        <th>Permission</th>
                        <th>Admin</th>
                        <th>Front Desk</th>
                        <th>Accounting</th>
                        <th>Coffee Shop</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>Manage Users</td>
                        <td><i class="fa-solid fa-check text-success"></i></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>Reservations</td>
                        <td><i class="fa-solid fa-check text-success"></i></td>
                        <td><i class="fa-solid fa-check text-success"></i></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>Guest Folio</td>
                        <td><i class="fa-solid fa-check text-success"></i></td>
                        <td><i class="fa-solid fa-check text-success"></i></td>
                        <td><i class="fa-solid fa-check text-success"></i></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>POS System</td>
                        <td><i class="fa-solid fa-check text-success"></i></td>
                        <td></td>
                        <td></td>
                        <td><i class="fa-solid fa-check text-success"></i></td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection