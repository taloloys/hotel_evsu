@extends('layouts.app')

@section('title', 'RBAC Management')
@section('pageTitle', 'Role & Permission Control')
@section('pageSubtitle', 'Manage system access for hotel staff')

@section('content')

<div class="row g-4">

    <!-- ROLES CARD -->
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Roles</h5>
                    <i class="fa-solid fa-user-shield text-primary"></i>
                </div>

                <div class="list-group">

                    <div class="list-group-item d-flex justify-content-between">
                        Admin
                        <span class="badge bg-dark">Full Access</span>
                    </div>

                    <div class="list-group-item">Front Desk</div>
                    <div class="list-group-item">Accounting</div>
                    <div class="list-group-item">Coffee Shop</div>

                </div>

                <button class="btn btn-primary w-100 mt-3">
                    <i class="fa-solid fa-plus me-2"></i>
                    Add Role
                </button>

            </div>

        </div>

    </div>

    <!-- PERMISSIONS CARD -->
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Permissions</h5>
                    <i class="fa-solid fa-key text-warning"></i>
                </div>

                <div class="list-group">

                    <div class="list-group-item">manage-users</div>
                    <div class="list-group-item">manage-reservations</div>
                    <div class="list-group-item">view-folio</div>
                    <div class="list-group-item">process-checkout</div>
                    <div class="list-group-item">manage-inventory</div>

                </div>

                <button class="btn btn-warning w-100 mt-3">
                    <i class="fa-solid fa-plus me-2"></i>
                    Add Permission
                </button>

            </div>

        </div>

    </div>

    <!-- USERS CARD -->
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Users</h5>
                    <i class="fa-solid fa-users text-success"></i>
                </div>

                <div class="list-group">

                    <div class="list-group-item d-flex justify-content-between">
                        Front Desk User
                        <span class="badge bg-primary">Front Desk</span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between">
                        Cashier
                        <span class="badge bg-info">Accounting</span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between">
                        Admin User
                        <span class="badge bg-dark">Admin</span>
                    </div>

                </div>

                <button class="btn btn-success w-100 mt-3">
                    <i class="fa-solid fa-user-plus me-2"></i>
                    Assign Role
                </button>

            </div>

        </div>

    </div>

</div>

<!-- ROLE PERMISSION MATRIX -->
<div class="card border-0 shadow-sm mt-4">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <h5 class="fw-bold mb-0">
                Role Permission Matrix
            </h5>

            <i class="fa-solid fa-table-list text-secondary"></i>

        </div>

        <div class="table-responsive">

            <table class="table table-bordered align-middle">

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