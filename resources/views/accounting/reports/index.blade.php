@extends('layouts.app')

@section('title', 'Accounting Reports')
@section('pageTitle', 'Financial Reports')
@section('pageSubtitle', 'Profit & Loss, Cash Flow, Revenue & Transactions')

@section('content')

<!-- TOP CONTROL BAR -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0" style="color: #504538; font-family: 'Franklin Gothic Medium', sans-serif;">Financial Reports</h5>
        <small class="text-muted">Statement details from {{ $dateFrom }} to {{ $dateTo }}</small>
    </div>
    <form action="{{ route('accounting.reports') }}" method="GET" id="reportsFilterForm">
        <div class="dropdown">
            <button class="btn bg-white d-flex align-items-center gap-2 px-3 position-relative shadow-sm"
                    type="button"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    style="height: 45px; border-radius: 0.5rem; border: 1px solid #c2a889; color: #504538; font-size: 1rem;">
                <i class="fa-solid fa-filter" style="color: #627e71;"></i>
                <span class="fw-semibold">Filter</span>
                @if($reportType !== 'ALL')
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                 onclick="event.stopPropagation()"
                 style="min-width: 300px; border-radius: 0.75rem; z-index: 1055;">

                <label class="form-label small mb-1 fw-semibold text-muted">Date From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                       class="form-control mb-3 shadow-none"
                       style="height:38px; border-radius:0.5rem; border: 1px solid #827567;">

                <label class="form-label small mb-1 fw-semibold text-muted">Date To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                       class="form-control mb-3 shadow-none"
                       style="height:38px; border-radius:0.5rem; border: 1px solid #827567;">

                <label class="form-label small mb-1 fw-semibold text-muted">Report Type</label>
                <select name="report_type" class="form-select mb-3 shadow-none" style="height:38px; border-radius:0.5rem; border: 1px solid #827567;">
                    <option value="ALL" {{ $reportType === 'ALL' ? 'selected' : '' }}>All Reports</option>
                    <option value="PL" {{ $reportType === 'PL' ? 'selected' : '' }}>Profit &amp; Loss</option>
                    <option value="CASH" {{ $reportType === 'CASH' ? 'selected' : '' }}>Cash Flow</option>
                    <option value="REVENUE" {{ $reportType === 'REVENUE' ? 'selected' : '' }}>Revenue Breakdown</option>
                    <option value="TX" {{ $reportType === 'TX' ? 'selected' : '' }}>Transactions List</option>
                </select>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn text-white w-50 fw-semibold" style="height: 38px; background-color: #334c42; border: none;">Apply</button>
                    <a href="{{ route('accounting.reports') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center fw-semibold" style="height: 38px; border: 1px solid #827567; color: #504538;">Reset</a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- TABS (SEGMENTED PILLS) -->
<ul class="nav nav-pills gap-2 mb-4 coffeeshop-nav-pills">

    @if($reportType === 'ALL' || $reportType === 'PL')
    <li class="nav-item">
        <button class="nav-link rounded-pill {{ ($reportType === 'ALL' || $reportType === 'PL') ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#pl">
            Profit & Loss
        </button>
    </li>
    @endif

    @if($reportType === 'ALL' || $reportType === 'CASH')
    <li class="nav-item">
        <button class="nav-link rounded-pill {{ ($reportType === 'CASH') ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#cash">
            Cash Flow
        </button>
    </li>
    @endif

    @if($reportType === 'ALL' || $reportType === 'REVENUE')
    <li class="nav-item">
        <button class="nav-link rounded-pill {{ ($reportType === 'REVENUE') ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#rev">
            Revenue Breakdown
        </button>
    </li>
    @endif

    @if($reportType === 'ALL' || $reportType === 'TX')
    <li class="nav-item">
        <button class="nav-link rounded-pill {{ ($reportType === 'TX') ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tx">
            Transactions List
        </button>
    </li>
    @endif

</ul>

<!-- TAB CONTENT -->
<div class="tab-content">

    <!-- PROFIT & LOSS -->
    @if($reportType === 'ALL' || $reportType === 'PL')
    <div class="tab-pane fade show {{ ($reportType === 'ALL' || $reportType === 'PL') ? 'active' : '' }}" id="pl">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">

                <div class="fw-bold mb-3" style="color: #504538;">Profit & Loss Statement</div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">Total Revenue (Posted Charges)</span>
                    <span class="fw-bold text-success">₱{{ number_format($revenue, 2) }}</span>
                </div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">Total Operating Expenses (Approved)</span>
                    <span class="fw-bold text-danger">₱{{ number_format($expenses, 2) }}</span>
                </div>

                <div class="d-flex justify-content-between pt-3 fw-bold fs-5">
                    <span style="color: #504538;">Net Profit / Loss</span>
                    <span class="{{ $netProfit >= 0 ? '' : 'text-danger' }}" style="{{ $netProfit >= 0 ? 'color: #504538;' : '' }}">
                        ₱{{ number_format($netProfit, 2) }}
                    </span>
                </div>

            </div>
        </div>
    </div>
    @endif

    <!-- CASH FLOW -->
    @if($reportType === 'ALL' || $reportType === 'CASH')
    <div class="tab-pane fade show {{ ($reportType === 'CASH') ? 'active' : '' }}" id="cash">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">

                <div class="fw-bold mb-3" style="color: #504538;">Cash Flow Statement</div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">Cash Inflow (Guest Collections/Payments)</span>
                    <span class="fw-bold text-success">₱{{ number_format($cashIn, 2) }}</span>
                </div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">Cash Outflow (Operating Expenses)</span>
                    <span class="fw-bold text-danger">₱{{ number_format($cashOut, 2) }}</span>
                </div>

                <div class="d-flex justify-content-between pt-3 fw-bold fs-5">
                    <span style="color: #504538;">Net Cash Flow</span>
                    <span class="{{ $netCashFlow >= 0 ? '' : 'text-danger' }}" style="{{ $netCashFlow >= 0 ? 'color: #504538;' : '' }}">
                        ₱{{ number_format($netCashFlow, 2) }}
                    </span>
                </div>

            </div>
        </div>
    </div>
    @endif

    <!-- REVENUE -->
    @if($reportType === 'ALL' || $reportType === 'REVENUE')
    <div class="tab-pane fade show {{ ($reportType === 'REVENUE') ? 'active' : '' }}" id="rev">
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="fw-bold mb-3">Revenue Breakdown by Department</div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">Rooms & Lodging (HOTEL)</span>
                    <span class="fw-bold">₱{{ number_format($revenueBreakdown['ROOMS'], 2) }}</span>
                </div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">F&B / Coffee Shop (RESTAURANT)</span>
                    <span class="fw-bold">₱{{ number_format($revenueBreakdown['RESTAURANT'], 2) }}</span>
                </div>

                @if($revenueBreakdown['TAX_SERVICE'] > 0 || $revenueBreakdown['OTHER'] > 0)
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">Government Taxes & Service Charges</span>
                    <span class="fw-bold">₱{{ number_format($revenueBreakdown['TAX_SERVICE'], 2) }}</span>
                </div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">Other General Charges</span>
                    <span class="fw-bold">₱{{ number_format($revenueBreakdown['OTHER'], 2) }}</span>
                </div>
                @else
                <div class="py-2">
                    <button class="btn btn-link btn-sm p-0 text-decoration-none text-muted" type="button" data-bs-toggle="collapse" data-bs-target="#zeroBreakdownRows">
                        <i class="fa-solid fa-chevron-down me-1"></i> Show additional zero-value categories
                    </button>
                    <div class="collapse mt-2" id="zeroBreakdownRows">
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span class="text-muted">Government Taxes & Service Charges</span>
                            <span class="fw-bold">₱{{ number_format($revenueBreakdown['TAX_SERVICE'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span class="text-muted">Other General Charges</span>
                            <span class="fw-bold">₱{{ number_format($revenueBreakdown['OTHER'], 2) }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <div class="d-flex justify-content-between pt-3 fw-bold fs-5">
                    <span>Total Revenue</span>
                    <span class="text-success">₱{{ number_format($totalRevenueBreakdown, 2) }}</span>
                </div>

            </div>
        </div>
    </div>
    @endif

    <!-- TRANSACTIONS -->
    @if($reportType === 'ALL' || $reportType === 'TX')
    <div class="tab-pane fade show {{ ($reportType === 'TX') ? 'active' : '' }}" id="tx">

        <div class="card border-0 shadow-sm">

            <div class="table-responsive">
                <table class="table mb-0 align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>Ref</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Guest</th>
                            <th>Date</th>
                            <th class="text-end">Amount</th>
                            <th>Posted By</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($transactions as $t)
                            <tr>
                                <td>{{ $t->charge_number ?? 'TX-' . $t->transaction_id }}</td>
                                <td>
                                    @if($t->credit_amount > 0)
                                        <span class="badge bg-success">Payment</span>
                                    @else
                                        <span class="badge bg-primary">Charge</span>
                                    @endif
                                </td>
                                <td>{{ $t->chargeCode->description ?? $t->reference_notes }}</td>
                                <td>
                                    @if($t->folio && $t->folio->guest)
                                        {{ $t->folio->guest->first_name }} {{ $t->folio->guest->last_name }}
                                    @else
                                        <span class="text-muted">General</span>
                                    @endif
                                </td>
                                <td>{{ $t->transaction_date->toDateString() }}</td>
                                <td class="text-end fw-bold {{ $t->credit_amount > 0 ? 'text-success' : '' }}">
                                    @if($t->credit_amount > 0)
                                        ₱{{ number_format($t->credit_amount, 2) }}
                                    @else
                                        ₱{{ number_format($t->charge_amount, 2) }}
                                    @endif
                                </td>
                                <td>
                                    @if($t->user && $t->user->username === 'system')
                                        System
                                    @elseif($t->user)
                                        {{ $t->user->full_name ?? $t->user->username }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No transactions found for the selected date range.</td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>

            @if($transactions->hasPages())
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top">
                <small class="text-muted">
                    Showing {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }} records
                </small>
                <div>
                    {{ $transactions->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif

        </div>

    </div>
    @endif

</div>

@endsection