@extends('layouts.app')

@section('title', 'Audit Logs')
@section('pageTitle', 'System Audit Trail')
@section('pageSubtitle', 'Track all system activities, user actions, and security events')

@section('content')

<!-- FILTER / ACTION BAR -->
<form action="{{ route('accounting.audit') }}" method="GET" class="card border-0 shadow-sm mb-4" id="auditFilterForm">
    <div class="card-body d-flex justify-content-between align-items-center">

        <div>
            <h5 class="fw-bold mb-0">Audit Logs</h5>
            <small class="text-muted">Complete system activity tracking</small>
        </div>

        <div class="d-flex align-items-center gap-2">

            <!-- SEARCH (live) -->
            <div style="width: 320px; border:1px solid #000; border-radius:.375rem; height: 45px;">
                <div class="input-group" style="height: 100%;">
                    <span class="input-group-text bg-white border-0 px-3">
                        <i class="fa-solid fa-magnifying-glass text-muted fs-5"></i>
                    </span>
                    <input
                        type="text"
                        name="search"
                        id="auditSearch"
                        class="form-control border-0 shadow-none py-2"
                        style="font-size: 1.05rem;"
                        placeholder="Search activity logs..."
                        value="{{ $search }}"
                        autocomplete="off">
                </div>
            </div>

            <!-- FILTER DROPDOWN -->
            <div class="dropdown">
                <button class="btn btn-outline-dark d-flex align-items-center gap-2 px-3 position-relative"
                        type="button"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        style="height: 45px; border-radius: 6px; border: 1px solid black; font-size: 1.05rem;">
                    <i class="fa-solid fa-filter fs-5"></i>
                    <span>Filter</span>
                    @if($userIdFilter !== 'ALL' || $actionFilter !== 'ALL')
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                     onclick="event.stopPropagation()"
                     style="min-width: 280px; border-radius: 8px; z-index: 1055;">

                    <label class="form-label small mb-1 fw-semibold text-muted">User</label>
                    <select name="user_id" class="form-select mb-3 shadow-none" style="height:38px; border-radius:4px; border: 1px solid black;">
                        <option value="ALL" {{ $userIdFilter === 'ALL' ? 'selected' : '' }}>All Users</option>
                        @foreach($users as $u)
                            <option value="{{ $u->user_id }}" {{ $userIdFilter == $u->user_id ? 'selected' : '' }}>{{ $u->full_name }}</option>
                        @endforeach
                    </select>

                    <label class="form-label small mb-1 fw-semibold text-muted">Action Type</label>
                    <select name="action_type" class="form-select mb-3 shadow-none" style="height:38px; border-radius:4px; border: 1px solid black;">
                        <option value="ALL" {{ $actionFilter === 'ALL' ? 'selected' : '' }}>All Actions</option>
                        @foreach($actionTypes as $type)
                            <option value="{{ $type }}" {{ $actionFilter === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-50" style="height: 38px;">Apply</button>
                        <a href="{{ route('accounting.audit') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center" style="height: 38px;">Reset</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>


<!-- LOG TABLE -->
<div class="card border-0 shadow-sm mb-3">

    <div class="table-responsive">

        <table class="table align-middle mb-0">

            <thead class="table-light">
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th class="text-end">IP Address</th>
                </tr>
            </thead>

            <tbody>

                @forelse($logs as $log)
                    <tr>
                        <td class="text-muted">{{ $log->timestamp->format('Y-m-d H:i') }}</td>
                        <td>
                            <div class="fw-semibold">{{ $log->user ? $log->user->full_name : 'System' }}</div>
                            <small class="text-muted">{{ $log->user ? $log->user->username : 'system' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $log->action_type }}</span>
                        </td>
                        <td>{{ $log->description }}</td>
                        <td><span class="badge bg-success">Success</span></td>
                        <td class="text-end text-muted">127.0.0.1</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No audit trail records found.</td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>

<!-- PAGINATION -->
<div class="d-flex justify-content-center mt-3">
    {{ $logs->appends(request()->query())->links() }}
</div>

@push('scripts')
<script>
(function () {
    const input = document.getElementById('auditSearch');
    if (!input) return;
    let timer;
    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            input.closest('form').requestSubmit();
        }, 400);
    });
})();
</script>
@endpush

@endsection