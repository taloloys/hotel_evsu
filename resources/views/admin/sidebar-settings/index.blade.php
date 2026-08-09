@extends('layouts.app')

@section('title', 'Sidebar Settings')
@section('pageTitle', 'Sidebar Display Settings')
@section('pageSubtitle', 'Manage your personal Super Admin navigation view')

@push('styles')
    <style>
        .setting-card {
            border: 1px solid var(--border-soft, #e7dccf);
            border-radius: 1.15rem;
            background: #ffffff;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-soft, 0 10px 30px rgba(0,0,0,0.05));
        }

        .setting-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 34px rgba(123, 17, 19, 0.08);
        }

        .setting-icon-box {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .setting-icon-box.visible {
            background: linear-gradient(135deg, rgba(123, 17, 19, 0.1), rgba(212, 168, 67, 0.15));
            color: var(--coffee-800, #7B1113);
        }

        .setting-icon-box.hidden {
            background: #f1f5f9;
            color: #64748b;
        }

        .form-switch .form-check-input {
            width: 3.2em;
            height: 1.7em;
            cursor: pointer;
        }

        .form-switch .form-check-input:checked {
            background-color: var(--coffee-800, #7B1113);
            border-color: var(--coffee-800, #7B1113);
        }
    </style>
@endpush

@section('content')

    <!-- TOP SUMMARY CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Configurable Modules</div>
                        <h3 class="mb-0 fw-bold">{{ count($modules) }}</h3>
                    </div>
                    <div class="bg-light p-3 rounded-3 text-primary">
                        <i class="fa-solid fa-cubes fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Visible in My Sidebar</div>
                        <h3 class="mb-0 fw-bold text-success">{{ $visibleCount }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                        <i class="fa-solid fa-eye fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center p-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Hidden from My Sidebar</div>
                        <h3 class="mb-0 fw-bold text-secondary">{{ $hiddenCount }}</h3>
                    </div>
                    <div class="bg-secondary bg-opacity-10 p-3 rounded-3 text-secondary">
                        <i class="fa-solid fa-eye-slash fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- INFO ALERT -->
    <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3">
        <i class="fa-solid fa-circle-info fs-3 text-info"></i>
        <div>
            <h6 class="fw-bold mb-1">Personal Navigation Preferences</h6>
            <span class="small">
                This page controls <strong>your personal Super Admin sidebar view</strong> only. Toggling off a section simplifies your side-nav without revoking your permissions or restricting direct URL access. Other staff members will continue to see their sidebar navigation based on their assigned roles.
            </span>
        </div>
    </div>

    <!-- MODULE SETTINGS LIST -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Sidebar Modules</h5>
                <small class="text-muted">Toggle navigation visibility for each major system module</small>
            </div>
            <span class="badge bg-dark rounded-pill px-3 py-2">Super Admin Personal View</span>
        </div>

        <div class="card-body px-4 pb-4 pt-2">
            <div class="row g-3">
                @foreach($modules as $module)
                    <div class="col-lg-6">
                        <div class="setting-card p-3 h-100 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="setting-icon-box {{ $module['is_visible'] ? 'visible' : 'hidden' }}">
                                    <i class="fa-solid {{ $module['icon'] }}"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h6 class="fw-bold mb-0 text-dark">{{ $module['name'] }}</h6>
                                        <span class="badge bg-light text-secondary border small">{{ $module['category'] }}</span>
                                    </div>
                                    <p class="text-muted small mb-0">{{ $module['description'] }}</p>
                                </div>
                            </div>

                            <div class="d-flex flex-column align-items-end gap-2 ps-3">
                                @if($module['is_visible'])
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-check me-1"></i> Visible
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-eye-slash me-1"></i> Hidden
                                    </span>
                                @endif

                                <form action="{{ route('admin.sidebar-settings.toggle', $module['key']) }}" method="POST" class="d-inline">
                                    @csrf
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               role="switch"
                                               id="switch-{{ $module['key'] }}"
                                               {{ $module['is_visible'] ? 'checked' : '' }}
                                               onchange="this.form.submit()">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection
