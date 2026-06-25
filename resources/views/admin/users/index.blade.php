@extends('layouts.app')

@section('title', 'Users Management')
@section('pageTitle', 'Users Management')
@section('pageSubtitle', 'Manage hotel staff accounts and role assignments')

@section('content')

{{-- TOAST CONTAINER --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">

    @if(session('success'))
        <div id="successToast" class="toast align-items-center text-white bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if($errors->has('cannot_disable_self'))
        <div id="errorToast" class="toast align-items-center text-white bg-danger border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ $errors->first('cannot_disable_self') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if($errors->any() && !$errors->has('cannot_disable_self'))
        <div id="validationToast" class="toast align-items-center text-white bg-danger border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="6000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <strong>Error:</strong> {{ $errors->first() }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif

</div>

<!-- KPI CARDS -->
<div class="row g-3 mb-3">

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Users</div>
                    <h4 class="mb-0">{{ $totalCount }}</h4>
                </div>
                <i class="fa-solid fa-users fa-2x text-primary"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Active Users</div>
                    <h4 class="mb-0 text-success">{{ $activeCount }}</h4>
                </div>
                <i class="fa-solid fa-user-check fa-2x text-success"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Inactive Users</div>
                    <h4 class="mb-0 text-danger">{{ $inactiveCount }}</h4>
                </div>
                <i class="fa-solid fa-user-slash fa-2x text-danger"></i>
            </div>
        </div>
    </div>

</div>

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-3">

    <div>
        <h5 class="fw-bold mb-0">Hotel Staff Users</h5>
        <small class="text-muted">Create accounts and manage roles &amp; access</small>
    </div>

    <!-- RIGHT ACTIONS (SEARCH + FILTER + ADD USER) -->
    <div class="d-flex gap-2 align-items-center">

        <!-- SEARCH -->
        <div style="width: 220px;">
            <input type="text"
                id="userSearchInput"
                class="form-control form-control-sm"
                placeholder="Search users..."
                autocomplete="off">
        </div>

        <!-- FILTER ICON DROPDOWN -->
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown">
                <i class="fa-solid fa-filter"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 240px;">

                <label class="form-label small mb-1">Role</label>
                <select id="filterRoleSelect" class="form-select form-select-sm mb-3">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        @php
                            $filterRoleLabel = match($role->role_name) {
                                'ADMIN' => 'Admin',
                                'FRONT_DESK' => 'Front Desk',
                                'ACCOUNTING' => 'Accounting',
                                'CAFETERIA' => 'Coffee Shop',
                                default => $role->role_name
                            };
                        @endphp
                        <option value="{{ $filterRoleLabel }}">{{ $filterRoleLabel }}</option>
                    @endforeach
                </select>

                <div class="d-flex gap-2">
                    <button id="filterApplyBtn" class="btn btn-primary btn-sm w-50">Apply</button>
                    <button id="filterResetBtn" class="btn btn-light btn-sm w-50">Reset</button>
                </div>

            </div>
        </div>

        <!-- ADD USER BUTTON -->
        <button id="add-user-btn"
                class="btn btn-primary px-3 py-2 fw-semibold"
                data-bs-toggle="modal"
                data-bs-target="#addUserModal">
            <i class="fa-solid fa-user-plus me-2"></i>
            Add User
        </button>

    </div>

</div>

<!-- USERS TABLE -->
<div class="card border-0 shadow-sm">

    <div class="card-body p-0">

        <div class="table-responsive">

            <table id="usersTable" class="table align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="ps-3">User</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 130px;">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($users as $user)
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
                        <tr
                            data-fullname="{{ strtolower($user->full_name) }}"
                            data-username="{{ strtolower($user->username) }}"
                            data-role="{{ strtolower($roleLabel) }}"
                            data-status="{{ $user->is_active ? 'active' : 'disabled' }}"
                            @if(! $user->is_active) class="opacity-75 bg-light-subtle" @endif>
                            <td class="ps-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                                         style="width:42px;height:42px;">
                                        <i class="fa-solid fa-user text-secondary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $user->full_name }}</div>
                                        <small class="text-muted">ID: U-{{ str_pad($user->user_id, 3, '0', STR_PAD_LEFT) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->username }}</td>
                            <td>
                                <span class="badge {{ $badgeClass }}">{{ $roleLabel }}</span>
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Active</span>
                                @else
                                    <span class="badge bg-danger"><i class="fa-solid fa-circle-xmark me-1"></i>Disabled</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{-- All buttons in a single flex row of equal width so they align identically across all rows --}}
                                <div class="d-flex justify-content-center gap-1">

                                    {{-- VIEW DETAILS --}}
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm px-2"
                                            title="View details"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewUserModal"
                                            data-user-id="{{ $user->user_id }}"
                                            data-full-name="{{ $user->full_name }}"
                                            data-username="{{ $user->username }}"
                                            data-role="{{ $roleLabel }}"
                                            data-role-badge="{{ $badgeClass }}"
                                            data-status="{{ $user->is_active ? 'Active' : 'Disabled' }}"
                                            data-status-class="{{ $user->is_active ? 'bg-success' : 'bg-danger' }}"
                                            data-permissions-names="{{ json_encode($user->permissions->pluck('permission_key')->toArray()) }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    {{-- EDIT USER --}}
                                    <button type="button"
                                            class="btn btn-outline-warning btn-sm px-2"
                                            title="Edit user"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editUserModal"
                                            data-user-id="{{ $user->user_id }}"
                                            data-full-name="{{ $user->full_name }}"
                                            data-username="{{ $user->username }}"
                                            data-role-id="{{ $user->role_id }}"
                                            data-permissions="{{ json_encode($user->permissions->pluck('permission_id')->toArray()) }}"
                                            data-update-url="{{ route('admin.users.update', $user) }}">
                                        <i class="fa-solid fa-user-gear"></i>
                                    </button>

                                    {{-- TOGGLE STATUS --}}
                                    @if(auth()->id() === $user->user_id)
                                        <button class="btn btn-outline-secondary btn-sm px-2"
                                                title="You cannot disable your own account"
                                                disabled>
                                            <i class="fa-solid fa-user-slash"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('admin.users.toggle', $user) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            @method('PATCH')
                                            @if($user->is_active)
                                                <button type="submit" class="btn btn-outline-danger btn-sm px-2" title="Disable user">
                                                    <i class="fa-solid fa-user-slash"></i>
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-outline-success btn-sm px-2" title="Enable user">
                                                    <i class="fa-solid fa-user-check"></i>
                                                </button>
                                            @endif
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="noResultsRow">
                            <td colspan="5" class="text-center py-4 text-muted">
                                No users found in the system.
                            </td>
                        </tr>
                    @endforelse

                    {{-- Shown by JS when filters hide all visible rows --}}
                    <tr id="noFilterResultsRow" style="display:none;">
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-magnifying-glass me-2"></i>No users match your search or filter.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- NOTE -->
<div class="mt-3 text-muted small">
    Note: Users must have a role assigned before accessing hotel modules.
</div>

<!-- =========================================================
     ADD USER MODAL
     ========================================================= -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addUserModalLabel">
                    <i class="fa-solid fa-user-plus me-2 text-primary"></i>Add New User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="add_full_name" class="form-label">Full Name</label>
                        <input type="text" id="add_full_name" name="full_name" class="form-control" value="{{ old('full_name') }}" placeholder="e.g. Juan dela Cruz" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_username" class="form-label">Username</label>
                        <input type="text" id="add_username" name="username" class="form-control" value="{{ old('username') }}" placeholder="e.g. jdelacruz" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_password" class="form-label">Password</label>
                        <input type="password" id="add_password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_role_id" class="form-label">Role</label>
                        <select id="add_role_id" name="role_id" class="form-select" required>
                            <option value="" disabled {{ old('role_id') === null ? 'selected' : '' }}>Select a role...</option>
                            @foreach($roles as $role)
                                @php
                                    $addRoleLabel = match($role->role_name) {
                                        'ADMIN' => 'Admin',
                                        'FRONT_DESK' => 'Front Desk',
                                        'ACCOUNTING' => 'Accounting',
                                        'CAFETERIA' => 'Coffee Shop',
                                        default => $role->role_name
                                    };
                                @endphp
                                <option value="{{ $role->role_id }}" {{ old('role_id') == $role->role_id ? 'selected' : '' }}>
                                    {{ $addRoleLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Direct Permission Overrides</label>
                        <div class="p-3 border rounded bg-light" style="max-height: 180px; overflow-y: auto;">
                            @foreach($permissions as $perm)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $perm->permission_id }}" 
                                           id="add_perm_{{ $perm->permission_id }}">
                                    <label class="form-check-label" for="add_perm_{{ $perm->permission_id }}">
                                        <span class="fw-semibold text-dark">{{ $perm->permission_key }}</span>
                                        <span class="text-muted small d-block" style="font-size: 0.75rem;">{{ $perm->description }} ({{ $perm->module }})</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-text small text-muted">Explicitly grant these permissions regardless of the user's role.</div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- =========================================================
     VIEW DETAILS MODAL
     ========================================================= -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="viewUserModalLabel">
                    <i class="fa-solid fa-id-card me-2 text-primary"></i>User Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body pt-3">

                <div class="text-center mb-4">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width:72px;height:72px;">
                        <i class="fa-solid fa-user fa-2x text-secondary"></i>
                    </div>
                    <h5 class="fw-bold mb-1" id="view-full-name">—</h5>
                    <span class="badge" id="view-role-badge">—</span>
                </div>

                <div class="list-group list-group-flush rounded">
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted small">User ID</span>
                        <span class="fw-semibold small" id="view-user-id">—</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted small">Username</span>
                        <span class="fw-semibold small" id="view-username">—</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted small">Role</span>
                        <span class="fw-semibold small" id="view-role">—</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between px-0 border-0 pb-1">
                        <span class="text-muted small">Status</span>
                        <span id="view-status" class="badge">—</span>
                    </div>
                    <div class="list-group-item px-0 border-0">
                        <span class="text-muted small d-block mb-2">Direct Permission Overrides</span>
                        <div id="view-direct-permissions" class="d-flex flex-wrap gap-1">
                            <!-- Populated dynamically -->
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<!-- =========================================================
     EDIT USER MODAL
     ========================================================= -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="editUserModalLabel">
                    <i class="fa-solid fa-user-gear me-2 text-warning"></i>Edit User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" id="editUserForm" action="">
                @csrf
                @method('PATCH')
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="edit_full_name" class="form-label">Full Name</label>
                        <input type="text" id="edit_full_name" name="full_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Username</label>
                        <input type="text" id="edit_username" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_password" class="form-label">
                            New Password
                            <span class="text-muted small">(leave blank to keep current)</span>
                        </label>
                        <input type="password" id="edit_password" name="password" class="form-control" placeholder="Minimum 6 characters">
                    </div>

                    <div class="mb-3">
                        <label for="edit_role_id" class="form-label">Role</label>
                        <select id="edit_role_id" name="role_id" class="form-select" required>
                            @foreach($roles as $role)
                                @php
                                    $editRoleLabel = match($role->role_name) {
                                        'ADMIN' => 'Admin',
                                        'FRONT_DESK' => 'Front Desk',
                                        'ACCOUNTING' => 'Accounting',
                                        'CAFETERIA' => 'Coffee Shop',
                                        default => $role->role_name
                                    };
                                @endphp
                                <option value="{{ $role->role_id }}">{{ $editRoleLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Direct Permission Overrides</label>
                        <div class="p-3 border rounded bg-light" style="max-height: 180px; overflow-y: auto;">
                            @foreach($permissions as $perm)
                                <div class="form-check mb-2">
                                    <input class="form-check-input permission-checkbox" 
                                           type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $perm->permission_id }}" 
                                           id="edit_perm_{{ $perm->permission_id }}">
                                    <label class="form-check-label" for="edit_perm_{{ $perm->permission_id }}">
                                        <span class="fw-semibold text-dark">{{ $perm->permission_key }}</span>
                                        <span class="text-muted small d-block" style="font-size: 0.75rem;">{{ $perm->description }} ({{ $perm->module }})</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-text small text-muted">Explicitly grant these permissions regardless of the user's role.</div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-white">Save Changes</button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function () {
        // ── Auto-show any pending toasts ──────────────────────────────────────
        document.querySelectorAll('.toast').forEach(function (el) {
            new bootstrap.Toast(el).show();
        });

        // ── View Details modal population ─────────────────────────────────────
        const viewUserModal = document.getElementById('viewUserModal');
        if (viewUserModal) {
            viewUserModal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                document.getElementById('view-user-id').textContent   = 'U-' + String(btn.dataset.userId).padStart(3, '0');
                document.getElementById('view-full-name').textContent = btn.dataset.fullName;
                document.getElementById('view-username').textContent  = btn.dataset.username;
                document.getElementById('view-role').textContent      = btn.dataset.role;

                const roleBadge = document.getElementById('view-role-badge');
                roleBadge.textContent = btn.dataset.role;
                roleBadge.className   = 'badge ' + btn.dataset.roleBadge;

                const statusBadge = document.getElementById('view-status');
                statusBadge.textContent = btn.dataset.status;
                statusBadge.className   = 'badge ' + btn.dataset.statusClass;

                const directPermissions = JSON.parse(btn.dataset.permissionsNames || '[]');
                const container = document.getElementById('view-direct-permissions');
                container.innerHTML = '';
                if (directPermissions.length === 0) {
                    container.innerHTML = '<span class="text-muted small">None</span>';
                } else {
                    directPermissions.forEach(name => {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-success-subtle text-success border border-success-subtle';
                        badge.textContent = name;
                        container.appendChild(badge);
                    });
                }
            });
        }

        // ── Edit User modal population ────────────────────────────────────────
        const editUserModal = document.getElementById('editUserModal');
        if (editUserModal) {
            editUserModal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                document.getElementById('edit_full_name').value = btn.dataset.fullName;
                document.getElementById('edit_username').value  = btn.dataset.username;
                document.getElementById('edit_password').value  = '';
                document.getElementById('edit_role_id').value   = btn.dataset.roleId;
                document.getElementById('editUserForm').action  = btn.dataset.updateUrl;

                // Populate checkboxes
                const userPermissions = JSON.parse(btn.dataset.permissions || '[]');
                document.querySelectorAll('.permission-checkbox').forEach(cb => {
                    cb.checked = userPermissions.includes(parseInt(cb.value));
                });
            });
        }

        // ── Search & Filter logic ─────────────────────────────────────────────
        const searchInput      = document.getElementById('userSearchInput');
        const roleSelect       = document.getElementById('filterRoleSelect');
        const applyBtn         = document.getElementById('filterApplyBtn');
        const resetBtn         = document.getElementById('filterResetBtn');
        const tbody            = document.querySelector('#usersTable tbody');
        const noFilterRow      = document.getElementById('noFilterResultsRow');
        const dropdownToggleEl = document.querySelector('.dropdown [data-bs-toggle="dropdown"]');

        let activeRole = '';

        function applyFilters() {
            const query = searchInput.value.trim().toLowerCase();
            const rows  = tbody.querySelectorAll('tr[data-fullname]');
            let visible = 0;

            rows.forEach(function (row) {
                const matchSearch = !query ||
                    row.dataset.fullname.includes(query) ||
                    row.dataset.username.includes(query);

                const matchRole = !activeRole ||
                    row.dataset.role === activeRole.toLowerCase();

                const show = matchSearch && matchRole;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            if (noFilterRow) {
                noFilterRow.style.display = (rows.length > 0 && visible === 0) ? '' : 'none';
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }

        if (applyBtn) {
            applyBtn.addEventListener('click', function () {
                activeRole = roleSelect.value;
                applyFilters();
                if (dropdownToggleEl) {
                    const dropdown = bootstrap.Dropdown.getInstance(dropdownToggleEl);
                    if (dropdown) { dropdown.hide(); }
                }
            });
        }

        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                searchInput.value = '';
                roleSelect.value  = '';
                activeRole        = '';
                applyFilters();
            });
        }
    })();
</script>
@endpush