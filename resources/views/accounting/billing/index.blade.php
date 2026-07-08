@extends('layouts.app')

@section('title', 'Billing Management')
@section('pageTitle', 'Billing & Invoices')
@section('pageSubtitle', 'Manage guest bills, invoices, and payment status')

@section('content')

<!-- KPI ROW -->
<div class="row g-3 mb-4">

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Total Invoices</div>
                <div class="fw-bold fs-3">{{ $totalInvoices }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Closed (Paid)</div>
                <div class="fw-bold fs-3 text-success">{{ $paidCount }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Open (Pending)</div>
                <div class="fw-bold fs-3 text-warning">{{ $pendingCount }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Outstanding Balance</div>
                <div class="fw-bold fs-3 text-danger">₱{{ number_format($unpaidBalance, 2) }}</div>
            </div>
        </div>
    </div>

</div>


<!-- ACTION BAR -->
<form action="{{ route('accounting.billing') }}" method="GET" class="card border-1 shadow-sm mb-3">

    <div class="card-body d-flex justify-content-between align-items-center">

        <div>
            <div class="fw-bold">Invoices</div>
            <small class="text-muted">All guest billing records</small>
        </div>

        <div class="d-flex align-items-center gap-3">

            {{-- Filter --}}
            <div style="width: 220px; border: 1px solid">
                <select
                    name="status"
                    class="form-select"
                    style="height: 40px;"
                    onchange="this.form.requestSubmit ? this.form.requestSubmit() : this.form.submit()">

                    <option value="ALL" {{ $statusFilter === 'ALL' ? 'selected' : '' }}>
                        All Status
                    </option>

                    <option value="PAID" {{ $statusFilter === 'PAID' ? 'selected' : '' }}>
                        Paid
                    </option>

                    <option value="UNPAID" {{ $statusFilter === 'UNPAID' ? 'selected' : '' }}>
                        Unpaid / Pending
                    </option>

                </select>
            </div>

            {{-- Search --}}
            <div style="width: 300px; border: 1px solid;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fa-solid fa-search text-muted"></i>
                    </span>

                    <input
                        type="text"
                        name="search"
                        class="form-control border-start-0"
                        style="height: 40px;"
                        placeholder="Search invoice no. or guest..."
                        value="{{ $search }}"
                        autocomplete="off">
                </div>
            </div>


            {{-- Search Button --}}
            <button type="submit" class="btn btn-primary px-4" style="height: 40px;">
                <i class="fa-solid fa-search me-1"></i>
                Search
            </button>

        </div>

    </div>

</form>


<!-- TABLE -->
<div class="card border-1 shadow-sm">

    <div class="table-responsive">

        <table class="table align-middle mb-0">

            <thead class="table-light">
                <tr>
                    <th>Invoice / Folio No</th>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Arrival Date</th>
                    <th>Status</th>
                    <th class="text-end">Total Charges</th>
                    <th class="text-end">Balance</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse($folios as $folio)
                    <tr>
                        <td>{{ $folio->folio_number }}</td>
                        <td>{{ $folio->guest_name }}</td>
                        <td>Room {{ $folio->room_number }}</td>
                        <td>{{ $folio->date }}</td>
                        <td>
                            @if($folio->display_status === 'Paid')
                                <span class="badge bg-success">Paid</span>
                            @else
                                <span class="badge bg-danger">Unpaid</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-dark">₱{{ number_format($folio->total_amount, 2) }}</td>
                        <td class="text-end fw-bold {{ $folio->balance > 0 ? 'text-danger' : 'text-success' }}">
                            ₱{{ number_format($folio->balance, 2) }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('accounting.billing.show', $folio->folio_id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye me-1"></i> View Folio
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No billing records found.</td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection