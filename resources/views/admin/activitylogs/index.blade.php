@extends('layouts.app')

@section('title', 'Activity Logs')
@section('pageTitle', 'Activity Logs')
@section('pageSubtitle', 'System audit trail and user activity monitoring')

@section('content')

<!-- SUMMARY CARDS -->
<div class="row g-3 mb-3">

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Logs</div>
                    <div class="fw-bold fs-5">{{ number_format($totalCount) }}</div>
                </div>
                <i class="fa-solid fa-clock-rotate-left text-primary fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Today's Logs</div>
                    <div class="fw-bold fs-5">{{ number_format($todayCount) }}</div>
                </div>
                <i class="fa-solid fa-calendar-day text-success fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Audit Actions</div>
                    <div class="fw-bold fs-5">{{ number_format($auditCount) }}</div>
                </div>
                <i class="fa-solid fa-shield-halved text-warning fs-4"></i>
            </div>
        </div>
    </div>

</div>

<!-- TABLE CARD -->
<div class="card border-0 shadow-sm">

    <!-- HEADER (LEFT TITLE + RIGHT ACTIONS FIXED) -->
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">

        <!-- LEFT SIDE -->
        <div>
            <div class="fw-semibold">Recent Activity</div>
            <small class="text-muted">Latest system events</small>
        </div>

        <!-- RIGHT SIDE (FILTER + EXPORT) -->
        <div class="d-flex gap-2">

            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#filterBox">
                <i class="fa-solid fa-filter me-1"></i>
                Filter
                @if(request()->anyFilled(['search', 'user_id', 'action_type', 'date_from', 'date_to']))
                    <span class="badge bg-primary ms-1">Active</span>
                @endif
            </button>

            <a href="{{ route('admin.activitylogs.export', request()->query()) }}" class="btn btn-outline-dark btn-sm">
                <i class="fa-solid fa-download me-1"></i>
                Export
            </a>

        </div>

    </div>

    <!-- FILTER PANEL -->
    <div class="collapse {{ request()->anyFilled(['search', 'user_id', 'action_type', 'date_from', 'date_to']) ? 'show' : '' }} border-top" id="filterBox">
        <div class="p-3 bg-light">
            <form method="GET" action="{{ route('admin.activitylogs') }}">
                <div class="row g-2">

                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Search Keywords</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search description, user..." value="{{ $filters['search'] ?? '' }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">User</label>
                        <select name="user_id" class="form-select form-select-sm">
                            <option value="all" {{ ($filters['user_id'] ?? 'all') === 'all' ? 'selected' : '' }}>All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->user_id }}" {{ ($filters['user_id'] ?? '') == $user->user_id ? 'selected' : '' }}>
                                    {{ $user->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Action Type</label>
                        <select name="action_type" class="form-select form-select-sm">
                            <option value="all" {{ ($filters['action_type'] ?? 'all') === 'all' ? 'selected' : '' }}>All Types</option>
                            @foreach($actionTypes as $type)
                                <option value="{{ $type }}" {{ ($filters['action_type'] ?? '') === $type ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', $type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Date From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $filters['date_from'] ?? '' }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Date To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $filters['date_to'] ?? '' }}">
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-primary btn-sm w-100" title="Apply Filters">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            <a href="{{ route('admin.activitylogs') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">

            <thead class="table-light">
                <tr>
                    <th style="width: 20%;">User</th>
                    <th style="width: 15%;">Action Type</th>
                    <th style="width: 45%;">Description</th>
                    <th style="width: 20%;">Timestamp</th>
                </tr>
            </thead>

            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="fw-semibold">
                            {{ $log->user?->full_name ?? 'System' }}
                            <br>
                            <small class="text-muted fw-normal">@&nbsp;{{ $log->user?->username ?? 'N/A' }}</small>
                        </td>
                        <td>
                            @php
                                $badgeColor = match($log->action_type) {
                                    'LOGIN' => 'bg-info text-dark',
                                    'RESERVATION_CREATE' => 'bg-primary text-white',
                                    'CHECK_IN' => 'bg-success text-white',
                                    'ADD_CHARGE' => 'bg-warning text-dark',
                                    'PRINT_FOLIO' => 'bg-secondary text-white',
                                    'CLOSE_SHIFT' => 'bg-dark text-white',
                                    'ROOM_MODIFIED' => 'bg-danger text-white',
                                    default => 'bg-light text-dark'
                                };
                            @endphp
                            <span class="badge {{ $badgeColor }}">{{ str_replace('_', ' ', $log->action_type) }}</span>
                        </td>
                        <td>{{ $log->description }}</td>
                        <td class="text-muted">
                            {{ $log->timestamp ? $log->timestamp->format('M d, Y h:i A') : 'N/A' }}
                            <br>
                            <small class="text-muted text-xs d-block mt-1">
                                <i class="fa-regular fa-clock me-1"></i>{{ $log->timestamp ? $log->timestamp->diffForHumans() : '' }}
                            </small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-triangle-exclamation fs-3 mb-2 d-block text-secondary"></i>
                            No activity logs found matching the filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

    <!-- PAGINATION FOOTER -->
    @if($logs->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries
                </div>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    @endif

</div>

@endsection