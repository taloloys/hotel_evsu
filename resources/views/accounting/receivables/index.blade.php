@extends('layouts.app')

@section('title', 'Receivables')
@section('pageTitle', 'Accounts Receivable')
@section('pageSubtitle', 'Monitor outstanding guest balances and pending settlements')

@section('content')

<!-- KPI ROW -->
<div class="row g-3 mb-4">

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Receivables</div>
                <div class="fw-bold fs-3 text-danger">₱{{ number_format($totalReceivables, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Current (0–30 Days)</div>
                <div class="fw-bold fs-3 text-success">₱{{ number_format($currentReceivables, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Overdue (31–60 Days)</div>
                <div class="fw-bold fs-3 text-warning">₱{{ number_format($overdueReceivables, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Critical (>60 Days)</div>
                <div class="fw-bold fs-3 text-danger">₱{{ number_format($criticalReceivables, 2) }}</div>
            </div>
        </div>
    </div>

</div>


<!-- ACTION BAR -->
<form action="{{ route('accounting.receivables') }}" method="GET" class="card border-0 shadow-sm mb-3">

    <div class="card-body d-flex justify-content-between align-items-center">

        <div>
            <div class="fw-bold">Outstanding Accounts</div>
            <small class="text-muted">Guest balances requiring settlement</small>
        </div>

        <div class="d-flex gap-2">

            <!-- SEARCH -->
            <input type="text" name="search" class="form-control form-control-sm"
                   style="width: 220px;" placeholder="Search guest / room" value="{{ $search }}">

            <!-- FILTER -->
            <select name="status" class="form-select form-select-sm" style="width: 160px;" onchange="this.form.submit()">
                <option value="ALL" {{ $statusFilter === 'ALL' ? 'selected' : '' }}>All Status</option>
                <option value="CURRENT" {{ $statusFilter === 'CURRENT' ? 'selected' : '' }}>Current</option>
                <option value="OVERDUE" {{ $statusFilter === 'OVERDUE' ? 'selected' : '' }}>Overdue</option>
                <option value="CRITICAL" {{ $statusFilter === 'CRITICAL' ? 'selected' : '' }}>Critical</option>
            </select>

            <!-- SEARCH BUTTON -->
            <button type="submit" class="btn btn-primary btn-sm px-3">
                <i class="fa-solid fa-magnifying-glass me-1"></i> Search
            </button>

        </div>

    </div>

</form>


<!-- TABLE -->
<div class="card border-0 shadow-sm">

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
                                <span class="badge bg-success">Current ({{ $rec->days_old }} days)</span>
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

</div>

@endsection