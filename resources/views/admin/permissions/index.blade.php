@extends('layouts.app')

@section('title', 'Permissions Management')
@section('pageTitle', 'Permissions')
@section('pageSubtitle', 'Control system access for hotel operations')

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

    @if ($errors->has('cannot_disable_manage_users'))
        <div id="errorToast" class="toast align-items-center text-white bg-danger border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ $errors->first('cannot_disable_manage_users') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if ($errors->any() && ! $errors->has('cannot_disable_manage_users'))
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

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h5 class="fw-bold mb-0">Permissions</h5>
        <small class="text-muted">Manage access rights for hotel staff roles</small>
    </div>

    <button class="btn btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
        <i class="fa-solid fa-plus me-1"></i>
        Add Permission
    </button>

</div>

<!-- PERMISSION LIST -->
<div class="card border-0 shadow-sm">

    <div class="card-body p-0">

        <div class="list-group list-group-flush">

            @forelse ($permissions as $permission)
                @php
                    $isSystem = $permission->permission_key === 'manage-users';
                    $isActive = $permission->is_active;

                    $moduleMap = [
                        'System'      => ['icon' => 'fa-user-shield',    'color' => 'text-dark',    'bg' => 'bg-light', 'badge' => 'bg-dark'],
                        'Front Desk'  => ['icon' => 'fa-bell-concierge', 'color' => 'text-primary', 'bg' => 'bg-primary bg-opacity-10', 'badge' => 'bg-primary'],
                        'Accounting'  => ['icon' => 'fa-receipt',        'color' => 'text-info',    'bg' => 'bg-info bg-opacity-10', 'badge' => 'bg-info text-dark'],
                        'Inventory'   => ['icon' => 'fa-boxes-stacked',  'color' => 'text-success', 'bg' => 'bg-success bg-opacity-10', 'badge' => 'bg-success'],
                        'POS'         => ['icon' => 'fa-mug-hot',        'color' => 'text-warning', 'bg' => 'bg-warning bg-opacity-10', 'badge' => 'bg-warning text-dark'],
                    ];

                    $meta = $moduleMap[$permission->module] ?? ['icon' => 'fa-key', 'color' => 'text-secondary', 'bg' => 'bg-light', 'badge' => 'bg-secondary'];
                @endphp

                <div class="list-group-item py-3 {{ $isActive ? '' : 'opacity-60' }}">

                    <div class="d-flex justify-content-between align-items-center">

                        {{-- PERMISSION INFO --}}
                        <div class="d-flex align-items-center">

                            <div class="{{ $meta['bg'] }} rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width:42px;height:42px;flex-shrink:0;">
                                <i class="fa-solid {{ $meta['icon'] }} {{ $meta['color'] }}"></i>
                            </div>

                            <div>
                                <div class="fw-semibold">
                                    {{ $permission->permission_key }}
                                    @if (! $isActive)
                                        <span class="badge bg-secondary ms-1" style="font-size:.65rem;">Disabled</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    {{ $permission->description }}
                                </small>
                            </div>

                        </div>

                        {{-- BADGES + ACTIONS --}}
                        <div class="d-flex align-items-center gap-2">

                            {{-- Module badge --}}
                            <span class="badge {{ $meta['badge'] }}">{{ $permission->module }}</span>

                            @if ($isSystem)
                                <span class="badge bg-dark">System</span>
                            @else
                                <span class="badge {{ $isActive ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $isActive ? 'Active' : 'Inactive' }}
                                </span>
                            @endif

                            {{-- Actions dropdown --}}
                            @if (! $isSystem)
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border"
                                            data-bs-toggle="dropdown"
                                            id="permissionActions{{ $permission->permission_id }}"
                                            aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0"
                                        aria-labelledby="permissionActions{{ $permission->permission_id }}">

                                        <li>
                                            <button class="dropdown-item"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editPermissionModal{{ $permission->permission_id }}">
                                                <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>
                                                Edit Permission
                                            </button>
                                        </li>

                                        <li><hr class="dropdown-divider"></li>

                                        <li>
                                            <button class="dropdown-item {{ $isActive ? 'text-danger' : 'text-success' }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#togglePermissionModal{{ $permission->permission_id }}">
                                                <i class="fa-solid {{ $isActive ? 'fa-ban' : 'fa-circle-check' }} me-2"></i>
                                                {{ $isActive ? 'Disable Permission' : 'Enable Permission' }}
                                            </button>
                                        </li>

                                    </ul>
                                </div>
                            @else
                                {{-- System key: locked indicator --}}
                                <span class="text-muted" title="System permission cannot be modified">
                                    <i class="fa-solid fa-lock fa-sm"></i>
                                </span>
                            @endif

                        </div>

                    </div>

                </div>
            @empty
                <div class="list-group-item py-4 text-center text-muted">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    No permissions found. Add a permission above.
                </div>
            @endforelse

        </div>

    </div>

</div>

<!-- FOOTER -->
<div class="mt-3 text-muted small">
    Permissions define what actions hotel staff can perform in the system.
</div>

{{-- =============================================
     MODALS: EDIT + TOGGLE per permission
     ============================================= --}}

@foreach ($permissions as $permission)
    @if ($permission->permission_key !== 'manage-users')

        {{-- EDIT PERMISSION MODAL --}}
        <div class="modal fade" id="editPermissionModal{{ $permission->permission_id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Permission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="{{ route('admin.permissions.update', $permission->permission_id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="modal-body">

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="edit_permission_key_{{ $permission->permission_id }}">Permission Key</label>
                                <input type="text"
                                       class="form-control"
                                       id="edit_permission_key_{{ $permission->permission_id }}"
                                       name="permission_key"
                                       value="{{ $permission->permission_key }}"
                                       required
                                       maxlength="100">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="edit_description_{{ $permission->permission_id }}">Description</label>
                                <textarea class="form-control"
                                          id="edit_description_{{ $permission->permission_id }}"
                                          name="description"
                                          rows="2"
                                          required
                                          maxlength="255">{{ $permission->description }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="edit_module_{{ $permission->permission_id }}">Module</label>
                                <select class="form-select" id="edit_module_{{ $permission->permission_id }}" name="module" required>
                                    <option value="System" {{ $permission->module === 'System' ? 'selected' : '' }}>System</option>
                                    <option value="Front Desk" {{ $permission->module === 'Front Desk' ? 'selected' : '' }}>Front Desk</option>
                                    <option value="Accounting" {{ $permission->module === 'Accounting' ? 'selected' : '' }}>Accounting</option>
                                    <option value="Inventory" {{ $permission->module === 'Inventory' ? 'selected' : '' }}>Inventory</option>
                                    <option value="POS" {{ $permission->module === 'POS' ? 'selected' : '' }}>POS</option>
                                </select>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning text-dark">
                                <i class="fa-solid fa-save me-1"></i>
                                Save Changes
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        {{-- TOGGLE STATUS MODAL --}}
        <div class="modal fade" id="togglePermissionModal{{ $permission->permission_id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">

                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">
                            {{ $permission->is_active ? 'Disable Permission' : 'Enable Permission' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body pt-2">
                        <p class="text-muted mb-0">
                            @if ($permission->is_active)
                                Are you sure you want to <strong>disable</strong> the
                                <strong>{{ $permission->permission_key }}</strong> permission?
                                Staff with roles that have this permission will temporarily lose access to this functionality.
                            @else
                                Re-enable the
                                <strong>{{ $permission->permission_key }}</strong> permission?
                                Staff with roles that have this permission will regain access.
                            @endif
                        </p>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('admin.permissions.toggle', $permission->permission_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="btn btn-sm {{ $permission->is_active ? 'btn-danger' : 'btn-success' }}">
                                <i class="fa-solid {{ $permission->is_active ? 'fa-ban' : 'fa-circle-check' }} me-1"></i>
                                {{ $permission->is_active ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    @endif
@endforeach

{{-- =============================================
     ADD PERMISSION MODAL
     ============================================= --}}
<div class="modal fade" id="addPermissionModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="add_permission_key">Permission Name (Key)</label>
                        <input type="text"
                               class="form-control @error('permission_key') is-invalid @enderror"
                               id="add_permission_key"
                               name="permission_key"
                               placeholder="e.g. manage-users"
                               value="{{ old('permission_key') }}"
                               required
                               maxlength="100">
                        @error('permission_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="add_description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="add_description"
                                  name="description"
                                  rows="2"
                                  placeholder="Short permission description"
                                  required
                                  maxlength="255">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="add_module">Module</label>
                        <select class="form-select @error('module') is-invalid @enderror" id="add_module" name="module" required>
                            <option value="System" {{ old('module') === 'System' ? 'selected' : '' }}>System</option>
                            <option value="Front Desk" {{ old('module') === 'Front Desk' ? 'selected' : '' }}>Front Desk</option>
                            <option value="Accounting" {{ old('module') === 'Accounting' ? 'selected' : '' }}>Accounting</option>
                            <option value="Inventory" {{ old('module') === 'Inventory' ? 'selected' : '' }}>Inventory</option>
                            <option value="POS" {{ old('module') === 'POS' ? 'selected' : '' }}>POS</option>
                        </select>
                        @error('module')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-dark">
                        <i class="fa-solid fa-save me-1"></i>
                        Save
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toast').forEach(function (el) {
            new bootstrap.Toast(el).show();
        });
    });
</script>
@endpush