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

                <form method="GET" action="{{ route('admin.activitylogs') }}" class="d-flex align-items-center gap-2 m-0 flex-wrap" id="activityLogsForm">
                    
                    <!-- SEARCH -->
                    <div style="width: 300px;">
                        <div class="input-group"
                             style="border: 1px solid #000000; border-radius: 6px; overflow: hidden; height: 38px;">
                            <span class="input-group-text bg-white border-0">
                                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                            </span>
                            <input type="text"
                                   id="activityLogSearchInput"
                                   name="search"
                                   class="form-control border-0 shadow-none"
                                   value="{{ $filters['search'] ?? '' }}"
                                   placeholder="Search description, user..."
                                   autocomplete="off">
                        </div>
                    </div>

                    <!-- FILTER DROPDOWN -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary d-flex align-items-center gap-1 px-3 position-relative"
                                type="button"
                                data-bs-toggle="dropdown"
                                style="height: 38px; border-radius: 6px; border: 1px solid;">
                            <i class="fa-solid fa-filter"></i>
                            <span>Filter</span>
                            @if(request()->anyFilled(['user_id','action_type','date_from','date_to']))
                                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                            @endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                             onclick="event.stopPropagation()"
                             style="min-width: 280px; border-radius: 8px;">

                            <!-- USER -->
                            <label class="form-label small mb-1 fw-semibold">User</label>
                            <select name="user_id"
                                    class="form-select mb-3"
                                    style="height: 38px; border-radius: 6px;">
                                <option value="all">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->user_id }}"
                                        {{ ($filters['user_id'] ?? '') == $user->user_id ? 'selected' : '' }}>
                                        {{ $user->full_name }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- ACTION TYPE -->
                            <label class="form-label small mb-1 fw-semibold">Action Type</label>
                            <select name="action_type"
                                    class="form-select mb-3"
                                    style="height: 38px; border-radius: 6px;">
                                <option value="all">All Types</option>
                                @foreach($actionTypes as $type)
                                    <option value="{{ $type }}"
                                        {{ ($filters['action_type'] ?? '') === $type ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', $type) }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- DATE FROM -->
                            <label class="form-label small mb-1 fw-semibold">Date From</label>
                            <input type="date"
                                   name="date_from"
                                   class="form-control mb-3"
                                   style="height: 38px; border-radius: 6px;"
                                   value="{{ $filters['date_from'] ?? '' }}">

                            <!-- DATE TO -->
                            <label class="form-label small mb-1 fw-semibold">Date To</label>
                            <input type="date"
                                   name="date_to"
                                   class="form-control mb-3"
                                   style="height: 38px; border-radius: 6px;"
                                   value="{{ $filters['date_to'] ?? '' }}">

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-50" style="height: 38px;">Apply</button>
                                <a href="{{ route('admin.activitylogs') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center" style="height: 38px;">Reset</a>
                            </div>
                        </div>
                    </div>

                </form>

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

@push('scripts')
<script>
    (function () {
        const searchInput = document.getElementById('activityLogSearchInput');
        if (searchInput) {
            function debounce(func, wait) {
                let timeout;
                return function (...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            const form = searchInput.closest('form');
            if (form) {
                searchInput.addEventListener('input', debounce(function () {
                    if (form.requestSubmit) {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                }, 500));
            }
        }
    })();
</script>
@endpush

@endsection