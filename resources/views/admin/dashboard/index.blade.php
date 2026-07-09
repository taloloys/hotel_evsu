@extends('layouts.app')

@section('title', 'RBAC Management')
@section('pageTitle', 'Role & Permission Control')
@section('pageSubtitle', 'Manage system access for hotel staff')

@push('styles')
    <style>
        /* Custom list wrapper with scrolling and nice scrollbar */
        .dashboard-list-scroll {
            height: 350px;
            overflow-y: auto;
            padding-right: 4px;
            scrollbar-width: thin;
            scrollbar-color: var(--caramel) rgba(78, 52, 46, 0.12);
        }

        .dashboard-list-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .dashboard-list-scroll::-webkit-scrollbar-track {
            background: rgba(78, 52, 46, 0.05);
            border-radius: 10px;
        }

        .dashboard-list-scroll::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--caramel), var(--coffee-700));
            border-radius: 10px;
        }

        .dashboard-list-scroll::-webkit-scrollbar-thumb:hover {
            background: var(--coffee-800);
        }

        /* Matrix scrollable wrapper and sticky headers */
        .matrix-table-scroll {
            max-height: 450px;
            overflow: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--caramel) rgba(78, 52, 46, 0.12);
        }

        .matrix-table-scroll::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .matrix-table-scroll::-webkit-scrollbar-track {
            background: rgba(78, 52, 46, 0.05);
            border-radius: 10px;
        }

        .matrix-table-scroll::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--caramel), var(--coffee-700));
            border-radius: 10px;
        }

        .matrix-table-scroll thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #f8f9fa !important;
            box-shadow: inset 0 -2px 0 rgba(0, 0, 0, 0.1);
        }

        /* List hover effect */
        .dashboard-list-item-hover {
            transition: background-color 0.2s ease, padding-left 0.2s ease;
        }

        .dashboard-list-item-hover:hover {
            background-color: #fbf8f5 !important;
            padding-left: 8px !important;
        }

        /* Search input styles */
        .search-container {
            position: relative;
            margin-bottom: 12px;
        }

        .search-input {
            padding-left: 36px;
            border-radius: 10px;
            border: 1px solid var(--border-soft, #e7dccf);
            transition: all 0.2s ease;
            background-color: #fff;
        }

        .search-input:focus {
            border-color: var(--caramel);
            box-shadow: 0 0 0 0.2rem rgba(169, 113, 66, 0.15);
            outline: 0;
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #a97142;
            opacity: 0.7;
            font-size: 0.9rem;
        }
    </style>
@endpush

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

                <div class="card-header bg-white border-0 pb-1">
                    <h6 class="fw-bold mb-0">Roles</h6>
                </div>

                <div class="card-body">

                    <div class="search-container">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" id="role-search" class="form-control search-input" placeholder="Search roles...">
                    </div>

                    <div class="dashboard-list-scroll">
                        <div class="list-group">

                            @foreach($roles as $role)
                                @php
                                    $roleLabel = match ($role->role_name) {
                                        'ADMIN' => 'Admin',
                                        'FRONT_DESK' => 'Front Desk',
                                        'ACCOUNTING' => 'Accounting',
                                        'CAFETERIA' => 'Coffee Shop',
                                        default => ucwords(strtolower(str_replace('_', ' ', $role->role_name)))
                                    };
                                @endphp
                                <div class="list-group-item d-flex justify-content-between align-items-center px-2 py-2 border-0 rounded-2 mb-1 dashboard-list-item-hover role-item {{ $role->is_active ? '' : 'text-muted text-decoration-line-through' }}"
                                    data-search-term="{{ strtolower($roleLabel) }}">
                                    <span>{{ $roleLabel }}</span>
                                    @if(strtoupper($role->role_name) === 'ADMIN')
                                        <span class="badge bg-dark">Full Access</span>
                                    @elseif(!$role->is_active)
                                        <span class="badge bg-secondary" style="font-size: 0.65rem;">Disabled</span>
                                    @endif
                                </div>
                            @endforeach

                        </div>
                    </div>

                </div>

                <div class="card-footer bg-white border-0 pt-0">
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

                <div class="card-header bg-white border-0 pb-1">
                    <h6 class="fw-bold mb-0">Permissions</h6>
                </div>

                <div class="card-body">

                    <div class="search-container">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" id="permission-search" class="form-control search-input"
                            placeholder="Search permissions...">
                    </div>

                    <div class="dashboard-list-scroll">
                        <div class="list-group">

                            @foreach($permissions as $permission)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-2 py-2 border-0 rounded-2 mb-1 dashboard-list-item-hover permission-item {{ $permission->is_active ? '' : 'text-muted text-decoration-line-through' }}"
                                    data-search-term="{{ strtolower($permission->permission_key) }}">
                                    <span>{{ $permission->permission_key }}</span>
                                    @if(!$permission->is_active)
                                        <span class="badge bg-secondary" style="font-size: 0.65rem;">Disabled</span>
                                    @endif
                                </div>
                            @endforeach

                        </div>
                    </div>

                </div>

                <div class="card-footer bg-white border-0 pt-0">
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

                <div class="card-header bg-white border-0 pb-1">
                    <h6 class="fw-bold mb-0">Users</h6>
                </div>

                <div class="card-body">

                    <div class="search-container">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" id="user-search" class="form-control search-input" placeholder="Search users...">
                    </div>

                    <div class="dashboard-list-scroll">
                        <div class="list-group">

                            @foreach($users as $user)
                                @php
                                    $badgeClass = match ($user->role?->role_name) {
                                        'ADMIN' => 'bg-dark',
                                        'FRONT_DESK' => 'bg-primary',
                                        'ACCOUNTING' => 'bg-info text-dark',
                                        'CAFETERIA' => 'bg-warning text-dark',
                                        default => 'bg-secondary'
                                    };
                                    $roleLabel = match ($user->role?->role_name) {
                                        'ADMIN' => 'Admin',
                                        'FRONT_DESK' => 'Front Desk',
                                        'ACCOUNTING' => 'Accounting',
                                        'CAFETERIA' => 'Coffee Shop',
                                        default => $user->role?->role_name ?? '—'
                                    };
                                @endphp
                                <div class="list-group-item d-flex justify-content-between align-items-center px-2 py-2 border-0 rounded-2 mb-1 dashboard-list-item-hover user-item {{ $user->is_active ? '' : 'text-muted text-decoration-line-through' }}"
                                    data-search-term="{{ strtolower($user->full_name . ' ' . $roleLabel) }}">
                                    <div>
                                        {{ $user->full_name }}
                                        @if(!$user->is_active)
                                            <small class="text-secondary ms-1">(Disabled)</small>
                                        @endif
                                    </div>
                                    <span class="badge {{ $badgeClass }}">{{ $roleLabel }}</span>
                                </div>
                            @endforeach

                        </div>
                    </div>

                </div>

                <div class="card-footer bg-white border-0 pt-0">
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

            <div class="matrix-table-scroll border rounded-3">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>Permission</th>
                            @foreach($activeRoles as $role)
                                                    <th>
                                                        {{ match ($role->role_name) {
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
                            <tr class="matrix-row" data-search-term="{{ strtolower($permission->permission_key) }}">
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

    @push('scripts')
        <script>
            document.addEventListener('turbo:load', function () {
                // Helper function to setup live filter
                function setupLiveFilter(inputId, itemSelector) {
                    const searchInput = document.getElementById(inputId);
                    if (!searchInput) return;

                    searchInput.addEventListener('input', function () {
                        const query = this.value.toLowerCase().trim();
                        const items = document.querySelectorAll(itemSelector);

                        items.forEach(item => {
                            const term = item.getAttribute('data-search-term') || '';
                            if (term.includes(query)) {
                                item.classList.remove('d-none');
                            } else {
                                item.classList.add('d-none');
                            }
                        });
                    });
                }

                // Setup filter for each dashboard section
                setupLiveFilter('role-search', '.role-item');
                setupLiveFilter('permission-search', '.permission-item');
                setupLiveFilter('user-search', '.user-item');

                // Clear search inputs on load/reload to stay in sync with lists
                ['role-search', 'permission-search', 'user-search'].forEach(id => {
                    const input = document.getElementById(id);
                    if (input) {
                        input.value = '';
                    }
                });
            });
        </script>
    @endpush

@endsection