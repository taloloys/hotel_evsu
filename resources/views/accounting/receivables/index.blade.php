@extends('layouts.app')

@section('title', 'Receivables')
@section('pageTitle', 'Accounts Receivable')
@section('pageSubtitle', 'Monitor outstanding guest balances and pending settlements')

@section('content')

<!-- KPI ROW -->
<div class="row g-3 mb-4">

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm rounded-4">
            <div class="card-body">
                <div class="text-muted small mb-1">Total Receivables</div>
                <div class="fw-bold fs-4 text-danger">₱{{ number_format($totalReceivables, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm rounded-4">
            <div class="card-body">
                <div class="text-muted small mb-1">Current (0–30 Days)</div>
                <div class="fw-bold fs-4 text-success">₱{{ number_format($currentReceivables, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm rounded-4">
            <div class="card-body">
                <div class="text-muted small mb-1">Overdue (31–60 Days)</div>
                <div class="fw-bold fs-4 text-warning">₱{{ number_format($overdueReceivables, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm rounded-4">
            <div class="card-body">
                <div class="text-muted small mb-1">Critical (>60 Days)</div>
                <div class="fw-bold fs-4 text-danger">₱{{ number_format($criticalReceivables, 2) }}</div>
            </div>
        </div>
    </div>

</div>


<!-- ACTION BAR -->
<form action="{{ route('accounting.receivables') }}" method="GET" class="card border-1 shadow-sm rounded-4 mb-3" id="receivablesFilterForm">

    <div class="card-body d-flex justify-content-between align-items-center">

        <div>
            <div class="fw-bold" style="color: #504538;">Outstanding Accounts</div>
            <small class="text-muted">Guest balances requiring settlement</small>
        </div>

        <div class="d-flex align-items-center gap-2">

            <!-- SEARCH (live) -->
            <div style="width: 320px;">
                <div class="input-group shadow-sm" style="border: 1px solid #c2a889; border-radius: 0.5rem; overflow: hidden; height: 45px; background-color: #ffffff;">
                    <span class="input-group-text bg-white border-0 px-3">
                        <i class="fa-solid fa-magnifying-glass" style="color: #627e71;"></i>
                    </span>
                    <input
                        type="text"
                        name="search"
                        id="receivablesSearch"
                        class="form-control border-0 shadow-none py-2"
                        style="font-size: 1rem;"
                        placeholder="Search guest or room..."
                        value="{{ $search }}"
                        autocomplete="off">
                </div>
            </div>

            <!-- FILTER DROPDOWN -->
            <div class="dropdown">
                <button class="btn bg-white d-flex align-items-center gap-2 px-3 position-relative shadow-sm"
                        type="button"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        style="height: 45px; border-radius: 0.5rem; border: 1px solid #c2a889; color: #504538; font-size: 1rem;">
                    <i class="fa-solid fa-filter" style="color: #627e71;"></i>
                    <span class="fw-semibold">Filter</span>
                    @if($statusFilter !== 'ALL')
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                     onclick="event.stopPropagation()"
                     style="min-width: 260px; border-radius: 0.75rem; z-index: 1055;">

                    <label class="form-label small mb-1 fw-semibold text-muted">Age Status</label>
                    <select name="status" class="form-select mb-3 shadow-none" style="height:38px; border-radius:0.5rem; border: 1px solid #827567;">
                        <option value="ALL" {{ $statusFilter === 'ALL' ? 'selected' : '' }}>All Status</option>
                        <option value="CURRENT" {{ $statusFilter === 'CURRENT' ? 'selected' : '' }}>Current (0–30 days)</option>
                        <option value="OVERDUE" {{ $statusFilter === 'OVERDUE' ? 'selected' : '' }}>Overdue (31–60 days)</option>
                        <option value="CRITICAL" {{ $statusFilter === 'CRITICAL' ? 'selected' : '' }}>Critical (>60 days)</option>
                    </select>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn text-white w-50 fw-semibold" style="height: 38px; background-color: #334c42; border: none;">Apply</button>
                        <a href="{{ route('accounting.receivables') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center fw-semibold" style="height: 38px; border: 1px solid #827567; color: #504538;">Reset</a>
                    </div>
                </div>
            </div>

        </div>

    </div>

</form>


<!-- TABLE -->
<div class="card border-1 shadow-sm rounded-4 overflow-hidden">

    <div class="table-responsive">

        <table class="table align-middle mb-0">

            <thead style="background-color: #f8f3ed; border-bottom: 2px solid #c2a889;">
                <tr class="small fw-bold" style="color: #1a1a1a;">
                    <th class="ps-3">GUEST</th>
                    <th>ROOM</th>
                    <th>FOLIO NO</th>
                    <th>DUE DATE</th>
                    <th>AGE STATUS</th>
                    <th class="text-end">BALANCE</th>
                    <th class="text-center pe-3">ACTION</th>
                </tr>
            </thead>

            <tbody>

                @forelse($receivables as $rec)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td class="ps-3 fw-bold" style="color: #1a1a1a;">{{ $rec->guest_name }}</td>
                        <td style="color: #262626;">Room {{ $rec->room_number }}</td>
                        <td style="color: #262626;">{{ $rec->folio_number }}</td>
                        <td style="color: #262626;">{{ $rec->due_date }}</td>
                        <td>
                            @if($rec->status === 'Current')
                                <span class="badge bg-success-subtle text-success fw-semibold">Current @if($rec->days_old > 0)({{ $rec->days_old }} days)@endif</span>
                            @elseif($rec->status === 'Overdue')
                                <span class="badge bg-warning-subtle text-warning fw-semibold">Overdue ({{ $rec->days_old }} days)</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger fw-semibold">Critical ({{ $rec->days_old }} days)</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-danger">₱{{ number_format($rec->balance, 2) }}</td>
                        <td class="text-center pe-3">
                            <a href="{{ route('accounting.billing.show', $rec->folio_id) }}" class="btn btn-sm px-3 fw-semibold shadow-sm" style="border: 1px solid #627e71; color: #1e332b; background-color: #e8f0ec; border-radius: 0.375rem;">
                                <i class="fa-solid fa-eye me-1"></i> View Folio
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4" style="color: #6b7280;">No outstanding accounts receivable found.</td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    @if($folios->hasPages())
    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top">
        <small class="text-muted">
            Showing {{ $folios->firstItem() }}–{{ $folios->lastItem() }} of {{ $folios->total() }} records
        </small>
        <div>
            {{ $folios->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
(function () {
    const input = document.getElementById('receivablesSearch');
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