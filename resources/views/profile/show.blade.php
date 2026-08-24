@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid px-0">

    <!-- HERO HEADER -->
    <div class="coffeeshop-hero mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; font-size: 1.5rem; background: rgba(255, 255, 255, 0.15) !important; color: #fff !important; backdrop-filter: blur(4px);">
                <i class="fa-solid fa-user-circle"></i>
            </div>
            <div>
                <h3 class="fw-bold text-white mb-0 font-display">My Profile</h3>
                <p class="text-white-50 mb-0 small">Manage your personal profile information and security settings.</p>
            </div>
        </div>
        <div>
            <span class="badge bg-white text-dark fs-6 px-3 py-2 shadow-sm rounded-pill font-body">
                <i class="fa-solid fa-shield-halved me-1" style="color: var(--htm-pineneedle, #334c42);"></i>
                {{ $user->role?->role_name === 'SUPER_ADMIN' ? 'Super Administrator' : ($user->role?->role_name === 'ADMIN' ? 'Administrator' : ($user->role?->description ?? $user->role?->role_name ?? 'User')) }}
            </span>
        </div>
    </div>

    <!-- MAIN CONTENT GRID -->
    <div class="row g-4">
        
        <!-- LEFT COLUMN: USER OVERVIEW CARD -->
        <div class="col-lg-4">
            <div class="card content-card h-100 p-4 border-0">
                <div class="card-body d-flex flex-column align-items-center text-center p-0">
                    
                    <!-- AVATAR BADGE -->
                    <div class="position-relative mb-3">
                        <div class="avatar-circle shadow-sm d-flex align-items-center justify-content-center text-white fw-bold fs-2"
                             style="width: 90px; height: 90px; border-radius: 50%; background: linear-gradient(135deg, #334c42 0%, #504538 100%); border: 4px solid #f8f3ed;">
                            {{ strtoupper(substr($user->full_name ?? $user->username ?? 'U', 0, 1)) }}
                        </div>
                        <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle p-2" title="Account Active">
                            <span class="visually-hidden">Active</span>
                        </span>
                    </div>

                    <h5 class="fw-bold text-dark mb-1 font-body">{{ $user->full_name }}</h5>
                    <p class="text-muted small mb-3">{{ '@' . $user->username }}</p>

                    <div class="mb-4">
                        <span class="badge rounded-pill px-3 py-2" style="background-color: #efe1cf; color: #504538; font-weight: 600;">
                            <i class="fa-solid fa-user-shield me-1"></i>
                            {{ $user->role?->role_name === 'SUPER_ADMIN' ? 'Super Administrator' : ($user->role?->role_name === 'ADMIN' ? 'Administrator' : ($user->role?->description ?? $user->role?->role_name ?? 'Staff')) }}
                        </span>
                    </div>

                    <hr class="w-100 my-2" style="border-color: #e7dccf;">

                    <!-- USER QUICK METADATA -->
                    <div class="w-100 text-start mt-3">
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                            <span class="text-muted small"><i class="fa-solid fa-fingerprint me-2 text-secondary"></i>User ID</span>
                            <span class="fw-semibold text-dark small">#{{ str_pad($user->user_id ?? $user->getKey(), 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                            <span class="text-muted small"><i class="fa-solid fa-envelope me-2 text-secondary"></i>Email</span>
                            <span class="fw-semibold text-dark small text-truncate ms-2" style="max-width: 170px;" title="{{ $user->email }}">{{ $user->email ?? 'Not set' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                            <span class="text-muted small"><i class="fa-solid fa-user-shield me-2 text-secondary"></i>Role Type</span>
                            <span class="fw-semibold text-dark small">{{ $user->role?->role_name ?? 'User' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted small"><i class="fa-solid fa-circle-check me-2 text-success"></i>Status</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">Active</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: FORM CARDS -->
        <div class="col-lg-8">
            <div class="d-flex flex-column gap-4">

                <!-- CARD 1: ACCOUNT DETAILS -->
                <div class="card content-card p-4 border-0">
                    <div class="card-body p-0">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                            <h5 class="fw-bold text-dark mb-0 font-body">
                                <i class="fa-solid fa-id-card me-2" style="color: #334c42;"></i>Account Details
                            </h5>
                            <span class="badge bg-light text-muted fw-normal"><i class="fa-solid fa-pen me-1"></i>Personal Info</span>
                        </div>

                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold text-secondary small">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-at"></i></span>
                                    <input type="text"
                                           id="username"
                                           class="form-control bg-light border-start-0 text-muted"
                                           value="{{ $user->username }}"
                                           disabled>
                                    <span class="input-group-text bg-light text-muted border-start-0" title="Username cannot be changed">
                                        <i class="fa-solid fa-lock text-secondary"></i>
                                    </span>
                                </div>
                                <small class="text-muted"><i class="fa-solid fa-circle-info me-1 mt-1"></i>Username is permanent and cannot be modified.</small>
                            </div>

                            <div class="mb-3">
                                <label for="full_name" class="form-label fw-semibold text-dark small">Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-user"></i></span>
                                    <input type="text"
                                           id="full_name"
                                           name="full_name"
                                           class="form-control @error('full_name') is-invalid @enderror"
                                           value="{{ old('full_name', $user->full_name) }}"
                                           required>
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold text-dark small">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email"
                                           id="email"
                                           name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $user->email) }}"
                                           placeholder="e.g. user@example.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-end">
                                <button type="submit" class="btn text-white px-4 py-2 rounded-3 shadow-sm" style="background-color: #334c42; border: none; font-weight: 600;">
                                    <i class="fa-solid fa-floppy-disk me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- CARD 2: CHANGE PASSWORD -->
                <div class="card content-card p-4 border-0">
                    <div class="card-body p-0">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                            <h5 class="fw-bold text-dark mb-0 font-body">
                                <i class="fa-solid fa-lock me-2" style="color: #504538;"></i>Security & Password
                            </h5>
                            <span class="badge bg-light text-muted fw-normal"><i class="fa-solid fa-key me-1"></i>Authentication</span>
                        </div>

                        <form action="{{ route('profile.password.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="current_password" class="form-label fw-semibold text-dark small">Current Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-shield-halved"></i></span>
                                    <input type="password"
                                           id="current_password"
                                           name="current_password"
                                           class="form-control @error('current_password') is-invalid @enderror"
                                           required>
                                    <button class="btn btn-outline-secondary toggle-password-btn" type="button" onclick="togglePasswordVisibility('current_password', this)" title="Toggle visibility">
                                        <i class="fa-solid fa-eye-slash"></i>
                                    </button>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label fw-semibold text-dark small">New Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-key"></i></span>
                                        <input type="password"
                                               id="password"
                                               name="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               placeholder="Min. 6 characters"
                                               required>
                                        <button class="btn btn-outline-secondary toggle-password-btn" type="button" onclick="togglePasswordVisibility('password', this)" title="Toggle visibility">
                                            <i class="fa-solid fa-eye-slash"></i>
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="password_confirmation" class="form-label fw-semibold text-dark small">Confirm New Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-check-double"></i></span>
                                        <input type="password"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               class="form-control"
                                               placeholder="Re-enter password"
                                               required>
                                        <button class="btn btn-outline-secondary toggle-password-btn" type="button" onclick="togglePasswordVisibility('password_confirmation', this)" title="Toggle visibility">
                                            <i class="fa-solid fa-eye-slash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <small class="text-muted"><i class="fa-solid fa-circle-info me-1"></i>Minimum 6 characters recommended.</small>
                                <button type="submit" class="btn text-white px-4 py-2 rounded-3 shadow-sm" style="background-color: #504538; border: none; font-weight: 600;">
                                    <i class="fa-solid fa-key me-2"></i>Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        const icon = btn.querySelector('i') || btn;
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }
</script>
@endpush

