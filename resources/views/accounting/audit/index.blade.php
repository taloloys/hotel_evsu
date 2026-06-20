@extends('layouts.app')

@section('title', 'Audit Logs')
@section('pageTitle', 'System Audit Trail')
@section('pageSubtitle', 'Track all system activities, user actions, and security events')

@section('content')

<!-- FILTER PANEL (SEPARATE FROM ACTIONS) -->
<form action="{{ route('accounting.audit') }}" method="GET" class="card border-0 shadow-sm mb-4">
    <div class="card-body">

        <div class="row g-3 align-items-end">

            <!-- USER FILTER -->
            <div class="col-md-3">
                <label class="form-label small text-muted">User</label>
                <select name="user_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="ALL" {{ $userIdFilter === 'ALL' ? 'selected' : '' }}>All Users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->user_id }}" {{ $userIdFilter == $u->user_id ? 'selected' : '' }}>
                            {{ $u->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- ACTION FILTER -->
            <div class="col-md-3">
                <label class="form-label small text-muted">Action Type</label>
                <select name="action_type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="ALL" {{ $actionFilter === 'ALL' ? 'selected' : '' }}>All Actions</option>
                    @foreach($actionTypes as $type)
                        <option value="{{ $type }}" {{ $actionFilter === $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- SEARCH -->
            <div class="col-md-3">
                <label class="form-label small text-muted">Search Description</label>
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="Search logs..." value="{{ $search }}">
            </div>

            <!-- SUBMIT -->
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Filter & Search
                </button>
            </div>

        </div>

    </div>
</form>

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-3">

    <div>
        <h5 class="fw-bold mb-0">Audit Logs</h5>
        <small class="text-muted">Complete system activity tracking</small>
    </div>

</div>

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

@endsection