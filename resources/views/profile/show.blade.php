@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid px-0">

    <!-- HERO HEADER -->
    <div class="coffeeshop-hero mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="fw-bold text-white mb-1">
                <i class="fa-solid fa-user-circle me-2"></i>My Profile
            </h3>
            <p class="text-white-50 mb-0">View and update your personal account information.</p>
        </div>
        <div>
            <span class="badge bg-light text-dark fs-6 px-3 py-2">
                <i class="fa-solid fa-shield-halved me-1 text-primary"></i>
                {{ $user->role?->role_name === 'SUPER_ADMIN' ? 'Super Administrator' : ($user->role?->role_name === 'ADMIN' ? 'Administrator' : ($user->role?->description ?? $user->role?->role_name)) }}
            </span>
        </div>
    </div>

    <!-- PROFILE CARD -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card content-card p-4">
                <div class="card-body">
                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fa-solid fa-id-card me-2 text-primary"></i>Account Details
                    </h5>

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold text-secondary">Username</label>
                            <input type="text"
                                   id="username"
                                   class="form-control bg-light"
                                   value="{{ $user->username }}"
                                   disabled>
                            <small class="text-muted">Username cannot be changed.</small>
                        </div>

                        <div class="mb-3">
                            <label for="full_name" class="form-label fw-semibold text-dark">Full Name <span class="text-danger">*</span></label>
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

                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold text-dark">Email Address</label>
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

                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa-solid fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
