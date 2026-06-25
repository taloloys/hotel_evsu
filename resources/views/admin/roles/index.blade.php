@extends('layouts.app')

@section('title', 'Roles Management')
@section('pageTitle', 'Roles Management')
@section('pageSubtitle', 'Manage hotel staff roles and access levels')

@section('content')

{{-- TOAST CONTAINER --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">

    @if (session('success'))
        <div id="successToast" class="toast align-items-center text-white bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if ($errors->has('cannot_disable_admin'))
        <div id="errorToast" class="toast align-items-center text-white bg-danger border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ $errors->first('cannot_disable_admin') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if ($errors->any() && ! $errors->has('cannot_disable_admin'))
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

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h5 class="fw-bold mb-0">Hotel Roles</h5>
        <small class="text-muted">Control access across hotel departments</small>
    </div>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
        <i class="fa-solid fa-plus me-1"></i>
        Add Role
    </button>

</div>

{{-- ROLE LIST --}}
<div class="card border-0 shadow-sm">

    <div class="card-body p-0">

        <div class="list-group list-group-flush">

            @forelse ($roles as $role)

                @php
                    $isAdmin   = strtoupper($role->role_name) === 'ADMIN';
                    $isActive  = $role->is_active;

                    $iconMap = [
                        'ADMIN'      => ['icon' => 'fa-user-shield',     'color' => 'text-dark',    'bg' => 'bg-light'],
                        'FRONT_DESK' => ['icon' => 'fa-bell-concierge',  'color' => 'text-primary', 'bg' => 'bg-primary bg-opacity-10'],
                        'ACCOUNTING' => ['icon' => 'fa-receipt',         'color' => 'text-info',    'bg' => 'bg-info bg-opacity-10'],
                        'CAFETERIA'  => ['icon' => 'fa-mug-hot',         'color' => 'text-success', 'bg' => 'bg-success bg-opacity-10'],
                        'HOUSEKEEPING' => ['icon' => 'fa-broom',         'color' => 'text-warning', 'bg' => 'bg-warning bg-opacity-10'],
                    ];

                    $meta = $iconMap[strtoupper($role->role_name)] ?? ['icon' => 'fa-id-badge', 'color' => 'text-secondary', 'bg' => 'bg-light'];
                @endphp

                <div class="list-group-item py-3 {{ $isActive ? '' : 'opacity-60' }}">

                    <div class="d-flex justify-content-between align-items-center">

                        {{-- ROLE INFO --}}
                        <div class="d-flex align-items-center">

                            <div class="{{ $meta['bg'] }} rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width:42px;height:42px;flex-shrink:0;">
                                <i class="fa-solid {{ $meta['icon'] }} {{ $meta['color'] }}"></i>
                            </div>

                            <div>
                                <div class="fw-semibold">
                                    {{ ucwords(strtolower(str_replace('_', ' ', $role->role_name))) }}
                                    @if (! $isActive)
                                        <span class="badge bg-secondary ms-1" style="font-size:.65rem;">Disabled</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    {{ $role->description ?? 'No description provided.' }}
                                </small>
                                @if($role->permissions->isNotEmpty())
                                    <div class="mt-2 d-flex flex-wrap gap-1">
                                        @foreach($role->permissions as $perm)
                                            <span class="badge bg-light text-secondary border border-secondary-subtle px-2 py-1" style="font-size:.65rem; font-weight: 500;">
                                                <i class="fa-solid fa-key me-1 fa-xs text-muted"></i>{{ $perm->permission_key }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="mt-2 text-muted small" style="font-size:.65rem;">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>No permissions assigned
                                    </div>
                                @endif
                            </div>

                        </div>

                        {{-- BADGES + ACTIONS --}}
                        <div class="d-flex align-items-center gap-2">

                            {{-- User count badge --}}
                            <span class="badge bg-light text-dark border">
                                <i class="fa-solid fa-users me-1 text-muted" style="font-size:.7rem;"></i>
                                {{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}
                            </span>

                            {{-- System badge for ADMIN --}}
                            @if ($isAdmin)
                                <span class="badge bg-dark">System</span>
                            @else
                                <span class="badge {{ $isActive ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $isActive ? 'Active' : 'Inactive' }}
                                </span>
                            @endif

                            {{-- Actions dropdown --}}
                            @if (! $isAdmin)
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border"
                                            data-bs-toggle="dropdown"
                                            id="roleActions{{ $role->role_id }}"
                                            aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0"
                                        aria-labelledby="roleActions{{ $role->role_id }}">

                                        <li>
                                            <button class="dropdown-item"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editRoleModal{{ $role->role_id }}">
                                                <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>
                                                Edit Role
                                            </button>
                                        </li>

                                        <li><hr class="dropdown-divider"></li>

                                        <li>
                                            <button class="dropdown-item {{ $isActive ? 'text-danger' : 'text-success' }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#toggleRoleModal{{ $role->role_id }}">
                                                <i class="fa-solid {{ $isActive ? 'fa-ban' : 'fa-circle-check' }} me-2"></i>
                                                {{ $isActive ? 'Disable Role' : 'Enable Role' }}
                                            </button>
                                        </li>

                                    </ul>
                                </div>
                            @else
                                {{-- Admin role: locked indicator --}}
                                <span class="text-muted" title="System role cannot be modified">
                                    <i class="fa-solid fa-lock fa-sm"></i>
                                </span>
                            @endif

                        </div>

                    </div>

                </div>

            @empty
                <div class="list-group-item py-4 text-center text-muted">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    No roles found. Add your first role above.
                </div>
            @endforelse

        </div>

    </div>

</div>

{{-- FOOTER NOTE --}}
<div class="mt-3 text-muted small">
    Roles define access permissions for each hotel department and system module.
</div>


{{-- =============================================
     MODALS: EDIT + TOGGLE per role
     ============================================= --}}

@foreach ($roles as $role)
    @if (strtoupper($role->role_name) !== 'ADMIN')

        {{-- EDIT ROLE MODAL --}}
        <div class="modal fade" id="editRoleModal{{ $role->role_id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="{{ route('admin.roles.update', $role->role_id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="modal-body">

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="edit_role_name_{{ $role->role_id }}">Role Name</label>
                                <input type="text"
                                       class="form-control"
                                       id="edit_role_name_{{ $role->role_id }}"
                                       name="role_name"
                                       value="{{ $role->role_name }}"
                                       required
                                       maxlength="50">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="edit_description_{{ $role->role_id }}">Description</label>
                                <textarea class="form-control"
                                          id="edit_description_{{ $role->role_id }}"
                                          name="description"
                                          rows="2"
                                          maxlength="255">{{ $role->description }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Assign Permissions</label>
                                <div class="border rounded p-3 bg-light" style="max-height: 250px; overflow-y: auto;">
                                    @foreach($permissions->groupBy('module') as $moduleName => $modulePermissions)
                                        <div class="mb-3">
                                            <h6 class="fw-bold border-bottom pb-1 text-muted small mb-2">{{ $moduleName }} Module</h6>
                                            <div class="row g-2">
                                                @foreach($modulePermissions as $perm)
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" 
                                                                   type="checkbox" 
                                                                   name="permissions[]" 
                                                                   value="{{ $perm->permission_id }}" 
                                                                   id="edit_perm_{{ $role->role_id }}_{{ $perm->permission_id }}"
                                                                   {{ $role->permissions->contains('permission_id', $perm->permission_id) ? 'checked' : '' }}>
                                                            <label class="form-check-label small" for="edit_perm_{{ $role->role_id }}_{{ $perm->permission_id }}">
                                                                <strong>{{ $perm->permission_key }}</strong>
                                                                <br><span class="text-muted d-block" style="font-size: 0.75rem; line-height: 1.1;">{{ $perm->description }}</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-save me-1"></i>
                                Save Changes
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        {{-- TOGGLE STATUS MODAL --}}
        <div class="modal fade" id="toggleRoleModal{{ $role->role_id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">

                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">
                            {{ $role->is_active ? 'Disable Role' : 'Enable Role' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body pt-2">
                        <p class="text-muted mb-0">
                            @if ($role->is_active)
                                Are you sure you want to <strong>disable</strong> the
                                <strong>{{ ucwords(strtolower(str_replace('_', ' ', $role->role_name))) }}</strong> role?
                                Staff with this role will lose access.
                            @else
                                Re-enable the
                                <strong>{{ ucwords(strtolower(str_replace('_', ' ', $role->role_name))) }}</strong> role?
                                Staff with this role will regain access.
                            @endif
                        </p>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('admin.roles.toggle', $role->role_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="btn btn-sm {{ $role->is_active ? 'btn-danger' : 'btn-success' }}">
                                <i class="fa-solid {{ $role->is_active ? 'fa-ban' : 'fa-circle-check' }} me-1"></i>
                                {{ $role->is_active ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    @endif
@endforeach


{{-- =============================================
     ADD ROLE MODAL
     ============================================= --}}
<div class="modal fade" id="addRoleModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="add_role_name">Role Name</label>
                        <input type="text"
                               class="form-control @error('role_name') is-invalid @enderror"
                               id="add_role_name"
                               name="role_name"
                               placeholder="e.g. FRONT_DESK_SUPERVISOR"
                               value="{{ old('role_name') }}"
                               required
                               maxlength="50">
                        @error('role_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="add_description">Description</label>
                        <textarea class="form-control"
                                  id="add_description"
                                  name="description"
                                  rows="2"
                                  placeholder="Short role description"
                                  maxlength="255">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assign Permissions</label>
                        <div class="border rounded p-3 bg-light" style="max-height: 250px; overflow-y: auto;">
                            @foreach($permissions->groupBy('module') as $moduleName => $modulePermissions)
                                <div class="mb-3">
                                    <h6 class="fw-bold border-bottom pb-1 text-muted small mb-2">{{ $moduleName }} Module</h6>
                                    <div class="row g-2">
                                        @foreach($modulePermissions as $perm)
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $perm->permission_id }}" 
                                                           id="add_perm_{{ $perm->permission_id }}"
                                                           {{ is_array(old('permissions')) && in_array($perm->permission_id, old('permissions')) ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="add_perm_{{ $perm->permission_id }}">
                                                        <strong>{{ $perm->permission_key }}</strong>
                                                        <br><span class="text-muted d-block" style="font-size: 0.75rem; line-height: 1.1;">{{ $perm->description }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i>
                        Save Role
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
    (function () {
        document.querySelectorAll('.toast').forEach(function (el) {
            new bootstrap.Toast(el).show();
        });
    })();
</script>
@endpush