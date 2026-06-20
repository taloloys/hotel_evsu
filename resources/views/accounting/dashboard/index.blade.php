@extends('layouts.app')

@section('title', 'Accounting Dashboard')
@section('pageTitle', 'Finance Overview')
@section('pageSubtitle', 'Hotel financial performance at a glance')

@section('content')

<!-- KPI ROW (DYNAMIC METRICS) -->
<div class="row g-3 mb-4">

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Revenue</div>
                <div class="fw-bold fs-4">₱{{ number_format($revenue, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Profit</div>
                <div class="fw-bold fs-4 text-primary">₱{{ number_format($profit, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Receivables</div>
                <div class="fw-bold fs-4 text-warning">₱{{ number_format($receivables, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Expenses</div>
                <div class="fw-bold fs-4 text-danger">₱{{ number_format($expenses, 2) }}</div>
            </div>
        </div>
    </div>

</div>

<!-- CASH SUMMARY -->
<div class="card border-0 shadow-sm mb-4">

    <div class="card-body py-3">

        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-bold">Cash Summary</div>
            <small class="text-muted">Net collections vs. operational costs</small>
        </div>

        <div class="d-flex justify-content-between">

            <div>
                <div class="text-muted small">Cash In (Collections)</div>
                <div class="fw-bold text-success">₱{{ number_format($cashIn, 2) }}</div>
            </div>

            <div>
                <div class="text-muted small">Cash Out (Expenses)</div>
                <div class="fw-bold text-danger">₱{{ number_format($cashOut, 2) }}</div>
            </div>

            <div>
                <div class="text-muted small">Net Flow</div>
                <div class="fw-bold text-primary">₱{{ number_format($netFlow, 2) }}</div>
            </div>

        </div>

    </div>

</div>

<!-- MAIN TABLE -->
<div class="card border-0 shadow-sm">

    <div class="card-body">

        <div class="mb-3">
            <div class="fw-bold">Recent Transactions</div>
            <small class="text-muted">Latest financial activity across hotel operations</small>
        </div>

        <div class="table-responsive">

            <table class="table align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th>Ref / Invoice</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Guest</th>
                        <th>Status</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($recentTransactions as $tx)
                        <tr>
                            <td>{{ $tx->charge_number ?? 'TX-' . $tx->transaction_id }}</td>
                            <td>
                                @if($tx->credit_amount > 0)
                                    <span class="badge bg-success">Payment</span>
                                @else
                                    <span class="badge bg-primary">Charge</span>
                                @endif
                            </td>
                            <td>{{ $tx->chargeCode->description ?? $tx->reference_notes }}</td>
                            <td>
                                @if($tx->folio && $tx->folio->guest)
                                    {{ $tx->folio->guest->first_name }} {{ $tx->folio->guest->last_name }}
                                @else
                                    <span class="text-muted">Non-guest</span>
                                @endif
                            </td>
                            <td><span class="badge bg-success">Posted</span></td>
                            <td class="text-end fw-bold {{ $tx->credit_amount > 0 ? 'text-success' : '' }}">
                                @if($tx->credit_amount > 0)
                                    ₱{{ number_format($tx->credit_amount, 2) }}
                                @else
                                    ₱{{ number_format($tx->charge_amount, 2) }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No recent transactions found.</td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection