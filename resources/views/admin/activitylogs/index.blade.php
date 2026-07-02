@extends('layouts.app')

@section('title', 'Activity Logs')
@section('pageTitle', 'Activity Logs')
@section('pageSubtitle', 'System audit trail and user activity monitoring')

@section('content')

<!-- SUMMARY CARDS -->
<div class="row g-3 mb-3">

    <div class="col-md-4">
        <div class="card border-1 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted large">Total Logs</div>
                    <div class="fw-bold fs-5">{{ number_format($totalCount) }}</div>
                </div>
                <i class="fa-solid fa-clock-rotate-left text-primary fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-1 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted large">Today's Logs</div>
                    <div class="fw-bold fs-5">{{ number_format($todayCount) }}</div>
                </div>
                <i class="fa-solid fa-calendar-day text-success fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-1 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted large">Audit Actions</div>
                    <div class="fw-bold fs-5">{{ number_format($auditCount) }}</div>
                </div>
                <i class="fa-solid fa-shield-halved text-warning fs-4"></i>
            </div>
        </div>
    </div>

</div>

<!-- TABLE CARD -->
<div class="card border-0 shadow-sm">

    <!-- =========================================================
     HEADER (IMPROVED UI ONLY)
     ========================================================= -->
    <div class="card-header bg-white border-0 py-3">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <!-- LEFT SIDE -->
            <div>
                <div class="fw-bold fs-5">Recent Activity</div>
                <small class="text-muted">Latest system events</small>
            </div>

            <!-- RIGHT SIDE ACTIONS -->
            <div class="d-flex align-items-center gap-2 flex-wrap">

                <!-- FILTER TOGGLE -->
                <button class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3"
                        style="height: 38px; border-radius: 6px;"
                        data-bs-toggle="collapse"
                        data-bs-target="#filterBox">

                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>

                    @if(request()->anyFilled(['search','user_id','action_type','date_from','date_to']))
                        <span class="badge bg-primary">Active</span>
                    @endif

                </button>

                <!-- EXPORT -->
                <a href="{{ route('admin.activitylogs.export', request()->query()) }}"
                class="btn btn-outline-dark d-flex align-items-center gap-2 px-3"
                style="height: 38px; border-radius: 6px;">

                    <i class="fa-solid fa-download"></i>
                    <span>Export</span>

                </a>

            </div>
        </div>
    </div>

    <!-- =========================================================
        FILTER PANEL (UI IMPROVED ONLY)
        ========================================================= -->
    <div class="collapse {{ request()->anyFilled(['search','user_id','action_type','date_from','date_to']) ? 'show' : '' }}"
        id="filterBox">

        <div class="p-3 bg-light border-top">

            <form method="GET" action="{{ route('admin.activitylogs') }}">

                <div class="row g-2 align-items-end">

                    <!-- SEARCH (USERS-STYLE UI) -->
                    <div class="col-md-3">

                        <label class="form-label small text-muted mb-1">Search Keywords</label>

                        <div class="input-group"
                            style="border: 1px solid #ced4da; border-radius: 6px; overflow: hidden; height: 45px;">

                            <span class="input-group-text bg-white border-0">
                                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                            </span>

                            <input type="text"
                                name="search"
                                class="form-control border-0 shadow-none"
                                value="{{ $filters['search'] ?? '' }}"
                                placeholder="Search description, user..."
                                autocomplete="off">
                        </div>

                    </div>

                    <!-- USER -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">User</label>
                        <select name="user_id"
                                class="form-select"
                                style="height: 45px; border-radius: 6px; border: 1px solid;">

                            <option value="all">All Users</option>

                            @foreach($users as $user)
                                <option value="{{ $user->user_id }}"
                                    {{ ($filters['user_id'] ?? '') == $user->user_id ? 'selected' : '' }}>
                                    {{ $user->full_name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <!-- ACTION TYPE -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Action Type</label>
                        <select name="action_type"
                                class="form-select"
                                style="height: 45px; border-radius: 6px; border: 1px solid;">

                            <option value="all">All Types</option>

                            @foreach($actionTypes as $type)
                                <option value="{{ $type }}"
                                    {{ ($filters['action_type'] ?? '') === $type ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', $type) }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <!-- DATE FROM -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Date From</label>
                        <input type="date"
                            name="date_from"
                            class="form-control"
                            style="height: 45px; border-radius: 6px; border: 1px solid;"
                            value="{{ $filters['date_from'] ?? '' }}">
                    </div>

                    <!-- DATE TO -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Date To</label>
                        <input type="date"
                            name="date_to"
                            class="form-control"
                             style="height: 45px; border-radius: 6px; border: 1px solid;"
                            value="{{ $filters['date_to'] ?? '' }}">
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="col-md-1 d-flex gap-1">

                        <!-- APPLY -->
                        <button type="submit"
                                class="btn btn-primary w-100"
                                 style="height: 45px; border-radius: 6px; border: 1px solid;">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>

                        <!-- RESET (CLEAR ONLY WHEN ACTIVE) -->
                        @if(request()->anyFilled(['search','user_id','action_type','date_from','date_to']))
                            <a href="{{ route('admin.activitylogs') }}"
                            class="btn btn-outline-secondary w-100"
                             style="height: 45px; border-radius: 6px; border: 1px solid;">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif

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
                                    'LOGOUT' => 'bg-secondary text-white',
                                    'RESERVATION_CREATE', 'USER_CREATED', 'SHIFT_SCHEDULE_CREATED', 'ADD_PRODUCT', 'CREDIT_ACCOUNT_CREATED' => 'bg-primary text-white',
                                    'CHECK_IN', 'FOLIO_PAID', 'ROOM_TRANSFER', 'FOLIO_CLOSED', 'FOLIO_REOPENED', 'POS_SALE' => 'bg-success text-white',
                                    'CHECK_OUT', 'RESERVATION_CANCEL', 'SHIFT_SCHEDULE_DELETED', 'DEACTIVATE_PRODUCT' => 'bg-secondary text-white',
                                    'ADD_CHARGE', 'USER_UPDATED', 'SHIFT_SCHEDULE_UPDATED', 'EDIT_PRODUCT', 'POS_REFUND', 'POS_TAB_TRANSFER', 'ACCOUNT_CHARGED' => 'bg-warning text-dark',
                                    'PRINT_FOLIO' => 'bg-secondary text-white',
                                    'CLOSE_SHIFT' => 'bg-dark text-white',
                                    'ROOM_MODIFIED', 'USER_STATUS_TOGGLED' => 'bg-danger text-white',
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