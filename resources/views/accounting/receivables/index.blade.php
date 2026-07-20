@extends('layouts.app')

@section('title', 'Receivables')
@section('pageTitle', 'Accounts Receivable')
@section('pageSubtitle', 'Monitor outstanding guest balances and pending settlements')

@section('content')

<!-- KPI ROW -->
<div class="row g-3 mb-4">

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Total Receivables</div>
                <div class="fw-bold fs-3 text-danger">₱{{ number_format($totalReceivables, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Current (0–30 Days)</div>
                <div class="fw-bold fs-3 text-success">₱{{ number_format($currentReceivables, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Overdue (31–60 Days)</div>
                <div class="fw-bold fs-3 text-warning">₱{{ number_format($overdueReceivables, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Critical (>60 Days)</div>
                <div class="fw-bold fs-3 text-danger">₱{{ number_format($criticalReceivables, 2) }}</div>
            </div>
        </div>
    </div>

</div>


<!-- ACTION BAR -->
<form action="{{ route('accounting.receivables') }}" method="GET" class="card border-1 shadow-sm mb-3" id="receivablesFilterForm">

    <div class="card-body d-flex justify-content-between align-items-center">

        <div>
            <div class="fw-bold">Outstanding Accounts</div>
            <small class="text-muted">Guest balances requiring settlement</small>
        </div>

        <div class="d-flex align-items-center gap-2">

            <!-- SEARCH (live) -->
            <div style="width: 320px; border: 1px solid #000000; border-radius: .375rem; height: 45px;">
                <div class="input-group" style="height: 100%;">
                    <span class="input-group-text bg-white border-0 px-3">
                        <i class="fa-solid fa-magnifying-glass text-muted fs-5"></i>
                    </span>
                    <input
                        type="text"
                        name="search"
                        id="receivablesSearch"
                        class="form-control border-0 shadow-none py-2"
                        style="font-size: 1.05rem;"
                        placeholder="Search guest or room..."
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
                    @if($statusFilter !== 'ALL')
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                     onclick="event.stopPropagation()"
                     style="min-width: 260px; border-radius: 8px; z-index: 1055;">

                    <label class="form-label small mb-1 fw-semibold text-muted">Age Status</label>
                    <select name="status" class="form-select mb-3 shadow-none" style="height:38px; border-radius:4px; border: 1px solid black;">
                        <option value="ALL" {{ $statusFilter === 'ALL' ? 'selected' : '' }}>All Status</option>
                        <option value="CURRENT" {{ $statusFilter === 'CURRENT' ? 'selected' : '' }}>Current (0–30 days)</option>
                        <option value="OVERDUE" {{ $statusFilter === 'OVERDUE' ? 'selected' : '' }}>Overdue (31–60 days)</option>
                        <option value="CRITICAL" {{ $statusFilter === 'CRITICAL' ? 'selected' : '' }}>Critical (>60 days)</option>
                    </select>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-50" style="height: 38px;">Apply</button>
                        <a href="{{ route('accounting.receivables') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center" style="height: 38px;">Reset</a>
                    </div>
                </div>
            </div>

        </div>

    </div>

</form>


<!-- TABLE -->
<div class="card border-1 shadow-sm">

    <div class="table-responsive">

        <table class="table align-middle mb-0">

            <thead class="table-light">
                <tr>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Folio No</th>
                    <th>Due Date</th>
                    <th>Age Status</th>
                    <th class="text-end">Balance</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse($receivables as $rec)
                    <tr>
                        <td>{{ $rec->guest_name }}</td>
                        <td>Room {{ $rec->room_number }}</td>
                        <td>{{ $rec->folio_number }}</td>
                        <td>{{ $rec->due_date }}</td>
                        <td>
                            @if($rec->status === 'Current')
                                <span class="badge bg-success">Current @if($rec->days_old > 0)({{ $rec->days_old }} days)@endif</span>
                            @elseif($rec->status === 'Overdue')
                                <span class="badge bg-warning text-dark">Overdue ({{ $rec->days_old }} days)</span>
                            @else
                                <span class="badge bg-danger">Critical ({{ $rec->days_old }} days)</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-danger">₱{{ number_format($rec->balance, 2) }}</td>
                        <td class="text-center">
                            <a href="{{ route('accounting.billing.show', $rec->folio_id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye me-1"></i> View Folio
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No outstanding accounts receivable found.</td>
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