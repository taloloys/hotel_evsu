@extends('layouts.app')

@section('title', 'Users Management')
@section('pageTitle', 'Users Management')
@section('pageSubtitle', 'Manage hotel staff accounts and role assignments')

@section('content')

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h5 class="fw-bold mb-0">Hotel Users</h5>
        <small class="text-muted">Create accounts and assign roles to staff</small>
    </div>

    <button class="btn btn-primary">
        <i class="fa-solid fa-user-plus me-2"></i>
        Add User
    </button>

</div>

<!-- USERS TABLE -->
<div class="card border-0 shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>

                </thead>

                <tbody>

                    <!-- USER 1 -->
                    <tr>

                        <td>
                            <div class="d-flex align-items-center">

                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2"
                                     style="width:38px;height:38px;">
                                    <i class="fa-solid fa-user text-secondary"></i>
                                </div>

                                <div class="fw-semibold">Front Desk Officer</div>

                            </div>
                        </td>

                        <td>frontdesk@hotel.com</td>

                        <td>
                            <span class="badge bg-primary">Front Desk</span>
                        </td>

                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>

                        <td class="text-center">

                            <div class="btn-group btn-group-sm">

                                <button class="btn btn-outline-primary">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                                <button class="btn btn-outline-warning">
                                    <i class="fa-solid fa-user-gear"></i>
                                </button>

                                <button class="btn btn-outline-danger">
                                    <i class="fa-solid fa-user-slash"></i>
                                </button>

                            </div>

                        </td>

                    </tr>

                    <!-- USER 2 -->
                    <tr>

                        <td>
                            <div class="d-flex align-items-center">

                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2"
                                     style="width:38px;height:38px;">
                                    <i class="fa-solid fa-user text-secondary"></i>
                                </div>

                                <div class="fw-semibold">Cashier</div>

                            </div>
                        </td>

                        <td>cashier@hotel.com</td>

                        <td>
                            <span class="badge bg-info text-dark">Accounting</span>
                        </td>

                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>

                        <td class="text-center">

                            <div class="btn-group btn-group-sm">

                                <button class="btn btn-outline-primary">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                                <button class="btn btn-outline-warning">
                                    <i class="fa-solid fa-user-gear"></i>
                                </button>

                                <button class="btn btn-outline-danger">
                                    <i class="fa-solid fa-user-slash"></i>
                                </button>

                            </div>

                        </td>

                    </tr>

                    <!-- USER 3 -->
                    <tr>

                        <td>
                            <div class="d-flex align-items-center">

                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2"
                                     style="width:38px;height:38px;">
                                    <i class="fa-solid fa-user text-secondary"></i>
                                </div>

                                <div class="fw-semibold">System Admin</div>

                            </div>
                        </td>

                        <td>admin@hotel.com</td>

                        <td>
                            <span class="badge bg-dark">Admin</span>
                        </td>

                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>

                        <td class="text-center">

                            <div class="btn-group btn-group-sm">

                                <button class="btn btn-outline-primary">
                                    <i class="fa-solid fa-eye"></i>
                                </button>

                                <button class="btn btn-outline-warning">
                                    <i class="fa-solid fa-user-gear"></i>
                                </button>

                                <button class="btn btn-outline-danger">
                                    <i class="fa-solid fa-user-slash"></i>
                                </button>

                            </div>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- FOOTER NOTE -->
<div class="mt-3 text-muted small">
    Users must be assigned a role to access hotel modules like Front Desk, Accounting, or Admin.
</div>

@endsection