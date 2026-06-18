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
                    <h4 class="mb-0">{{ $totalRolesCount }}</h4>
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
                    <h4 class="mb-0">{{ $totalPermissionsCount }}</h4>
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
                    <h4 class="mb-0">{{ $totalUsersCount }}</h4>
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

                    @foreach($roles as $role)
                        @php
                            $roleLabel = match($role->role_name) {
                                'ADMIN' => 'Admin',
                                'FRONT_DESK' => 'Front Desk',
                                'ACCOUNTING' => 'Accounting',
                                'CAFETERIA' => 'Coffee Shop',
                                default => ucwords(strtolower(str_replace('_', ' ', $role->role_name)))
                            };
                        @endphp
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 {{ $role->is_active ? '' : 'text-muted text-decoration-line-through' }}">
                            {{ $roleLabel }}
                            @if(strtoupper($role->role_name) === 'ADMIN')
                                <span class="badge bg-dark">Full Access</span>
                            @elseif(! $role->is_active)
                                <span class="badge bg-secondary" style="font-size: 0.65rem;">Disabled</span>
                            @endif
                        </div>
                    @endforeach

                </div>

            </div>

            <div class="card-footer bg-white border-0">
                <a href="{{ route('admin.roles') }}" class="btn btn-primary w-100">
                    <i class="fa-solid fa-plus me-2"></i>
                    Add Role
                </a>
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

                    @foreach($permissions as $permission)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 {{ $permission->is_active ? '' : 'text-muted text-decoration-line-through' }}">
                            {{ $permission->permission_key }}
                            @if(! $permission->is_active)
                                <span class="badge bg-secondary" style="font-size: 0.65rem;">Disabled</span>
                            @endif
                        </div>
                    @endforeach

                </div>

            </div>

            <div class="card-footer bg-white border-0">
                <a href="{{ route('admin.permissions') }}" class="btn btn-warning w-100 text-dark">
                    <i class="fa-solid fa-plus me-2"></i>
                    Add Permission
                </a>
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

                    @foreach($users as $user)
                        @php
                            $badgeClass = match($user->role?->role_name) {
                                'ADMIN' => 'bg-dark',
                                'FRONT_DESK' => 'bg-primary',
                                'ACCOUNTING' => 'bg-info text-dark',
                                'CAFETERIA' => 'bg-warning text-dark',
                                default => 'bg-secondary'
                            };
                            $roleLabel = match($user->role?->role_name) {
                                'ADMIN' => 'Admin',
                                'FRONT_DESK' => 'Front Desk',
                                'ACCOUNTING' => 'Accounting',
                                'CAFETERIA' => 'Coffee Shop',
                                default => $user->role?->role_name ?? '—'
                            };
                        @endphp
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 {{ $user->is_active ? '' : 'text-muted text-decoration-line-through' }}">
                            <div>
                                {{ $user->full_name }}
                                @if(! $user->is_active)
                                    <small class="text-secondary ms-1">(Disabled)</small>
                                @endif
                            </div>
                            <span class="badge {{ $badgeClass }}">{{ $roleLabel }}</span>
                        </div>
                    @endforeach

                </div>

            </div>

            <div class="card-footer bg-white border-0">
                <a href="{{ route('admin.users') }}" class="btn btn-success w-100">
                    <i class="fa-solid fa-user-plus me-2"></i>
                    Assign Role
                </a>
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
                        @foreach($activeRoles as $role)
                            <th>
                                {{ match($role->role_name) {
                                    'ADMIN' => 'Admin',
                                    'FRONT_DESK' => 'Front Desk',
                                    'ACCOUNTING' => 'Accounting',
                                    'CAFETERIA' => 'Coffee Shop',
                                    default => ucwords(strtolower(str_replace('_', ' ', $role->role_name)))
                                } }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @foreach($activePermissions as $permission)
                        <tr>
                            <td>
                                <strong>{{ $permission->permission_key }}</strong>
                                <br><small class="text-muted">{{ $permission->description }}</small>
                            </td>
                            @foreach($activeRoles as $role)
                                <td>
                                    @if($role->permissions->contains('permission_id', $permission->permission_id))
                                        <i class="fa-solid fa-check text-success fs-5"></i>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection