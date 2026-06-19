@extends('layouts.app')

@section('title', 'Shift Sales Report')
@section('pageTitle', 'Shift Sales Report')
@section('pageSubtitle', 'Detailed sales audit for shift session')

@section('content')

<style>
    @media print {
        body {
            background: #ffffff !important;
            color: #000000 !important;
            font-size: 11px !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .sidebar,
        .main-content > .card:first-child, /* topbar */
        .hide-on-print,
        .btn,
        .btn-group {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        .container-fluid {
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            margin-bottom: 20px !important;
            padding: 0 !important;
            background: transparent !important;
        }
        .card-body {
            padding: 0 !important;
        }
        .table-responsive {
            overflow: visible !important;
        }
        .table {
            border: 1px solid #000000 !important;
            width: 100% !important;
            margin-bottom: 20px !important;
        }
        .table th, .table td {
            border: 1px solid #000000 !important;
            padding: 4px 6px !important;
        }
        .card.bg-primary, .card.bg-success {
            background-color: #f8f9fa !important;
            color: #000000 !important;
            border: 1px solid #000000 !important;
            padding: 10px !important;
        }
        .card.bg-primary small, .card.bg-success small {
            color: #555555 !important;
        }
        .card.bg-primary h2, .card.bg-success h2 {
            color: #000000 !important;
            font-size: 18px !important;
        }
        .list-group-item {
            border-bottom: 1px solid #000000 !important;
            padding: 4px 0 !important;
        }
    }
</style>

<div class="container-fluid d-print-none">
    <div class="d-flex justify-content-between align-items-center mb-4 hide-on-print">
        <a href="{{ route('admin.shift-schedules') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Schedules
        </a>
        @if($shift)
            <button onclick="window.print()" class="btn btn-primary btn-sm px-3">
                <i class="fa-solid fa-print me-1"></i> Print / Save PDF
            </button>
        @endif
    </div>

    <!-- SHIFT INFO HEADER -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <span class="badge bg-primary-subtle text-primary mb-2">{{ $schedule->shift_name }}</span>
                    <h3 class="fw-bold mb-1">{{ $schedule->user->full_name }}</h3>
                    <p class="text-muted mb-0">Role: {{ $schedule->user->role?->role_name ?? 'Staff' }} | Shift Date: {{ $schedule->shift_date->format('F d, Y') }}</p>
                </div>
                <div class="text-end">
                    <span class="text-muted d-block small">Schedule Status</span>
                    <span class="badge {{ $schedule->status === 'ACTIVE' ? 'bg-success' : ($schedule->status === 'COMPLETED' ? 'bg-secondary' : 'bg-warning text-dark') }} fs-6">
                        {{ $schedule->status }}
                    </span>
                </div>
            </div>

            <hr class="my-4">

            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <span class="text-muted d-block small">Scheduled Time</span>
                    <span class="fw-semibold">
                        {{ Carbon\Carbon::parse($schedule->scheduled_start_time)->format('g:i A') }} - 
                        {{ Carbon\Carbon::parse($schedule->scheduled_end_time)->format('g:i A') }}
                    </span>
                </div>
                <div class="col-md-3 col-6">
                    <span class="text-muted d-block small">Actual Clock-In</span>
                    <span class="fw-semibold">
                        @if($shift)
                            {{ $shift->start_time->format('M d, Y g:i A') }}
                        @else
                            <span class="text-danger">Not Clocked In</span>
                        @endif
                    </span>
                </div>
                <div class="col-md-3 col-6">
                    <span class="text-muted d-block small">Actual Clock-Out</span>
                    <span class="fw-semibold">
                        @if($shift && $shift->end_time)
                            {{ $shift->end_time->format('M d, Y g:i A') }}
                        @elseif($shift)
                            <span class="text-success">Active Session</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </span>
                </div>
                <div class="col-md-3 col-6">
                    <span class="text-muted d-block small">Variance / Notes</span>
                    <span class="text-muted small">
                        {{ $schedule->notes ?: 'No schedule notes.' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if(!$shift)
        <!-- NO SESSION ALERTS -->
        <div class="card border-0 shadow-sm text-center py-5 rounded-4">
            <div class="card-body">
                <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 72px; height: 72px;">
                    <i class="fa-solid fa-clock-rotate-left fa-2x"></i>
                </div>
                <h5 class="fw-bold mb-1">Shift Session has not started</h5>
                <p class="text-muted mb-0">Sales reports and transaction audits are generated when the employee clocks in.</p>
            </div>
        </div>
    @else
        <!-- SALES SUMMARY CARDS -->
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body p-4">
                        <small class="text-white-50">Total Charges Generated</small>
                        <h2 class="fw-bold mt-2 mb-0">₱{{ number_format($salesSummary['total_charges'], 2) }}</h2>
                        <small class="opacity-75">Room posts & services</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body p-4">
                        <small class="text-white-50">Total Payments Collected</small>
                        <h2 class="fw-bold mt-2 mb-0">₱{{ number_format($salesSummary['total_payments'], 2) }}</h2>
                        <small class="opacity-75">Drawer cash & credit cards</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <small class="text-muted">Cash Drawer Payments</small>
                        <h2 class="fw-bold mt-2 mb-0 text-success">₱{{ number_format($salesSummary['cash_payments'], 2) }}</h2>
                        <small class="text-muted">Physical cash received</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <small class="text-muted">Card Drawer Payments</small>
                        <h2 class="fw-bold mt-2 mb-0 text-primary">₱{{ number_format($salesSummary['card_payments'], 2) }}</h2>
                        <small class="text-muted">Mastercard / VISA / Checks</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- REVENUE & CHARGES BREAKDOWN -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">Charges By Category</h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="list-group list-group-flush">
                            @forelse($salesSummary['by_category'] as $category => $amount)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="fw-medium text-secondary">{{ $category }}</span>
                                    <span class="fw-bold text-dark">₱{{ number_format($amount, 2) }}</span>
                                </div>
                            @empty
                                <div class="text-muted py-3 text-center">No charges logged in this shift.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- PAYMENT BREAKDOWN -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">Collections Breakdown</h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-medium text-secondary"><i class="fa-solid fa-money-bill-wave text-success me-2"></i>Cash Payments</span>
                                <span class="fw-bold text-dark">₱{{ number_format($salesSummary['cash_payments'], 2) }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-medium text-secondary"><i class="fa-solid fa-credit-card text-primary me-2"></i>Credit Card</span>
                                <span class="fw-bold text-dark">₱{{ number_format($salesSummary['card_payments'], 2) }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-medium text-secondary"><i class="fa-solid fa-money-check text-warning me-2"></i>Check Payments</span>
                                <span class="fw-bold text-dark">₱{{ number_format($salesSummary['check_payments'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SHIFT AUDIT LOG DETAILS -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">Shift Session Information</h5>
                    </div>
                    <div class="card-body pt-0 small">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Shift Session ID</span>
                                <span class="fw-semibold">#{{ $shift->shift_id }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Transactions Audited</span>
                                <span class="fw-semibold">{{ $transactions->count() }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Total Activity Logs</span>
                                <span class="fw-semibold">
                                    {{ App\Models\ActivityLog::where('description', 'like', "%Shift #{$shift->shift_id}%")->count() }} logs
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TRANSACTION AUDIT LIST -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold mb-0">Shift Transaction Audit Log</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Doc Number</th>
                                <th>Folio / Guest</th>
                                <th>Charge Code</th>
                                <th>Category</th>
                                <th>Payment Method</th>
                                <th>Reference Notes</th>
                                <th class="text-end">Charge</th>
                                <th class="text-end pe-3">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $tx)
                                <tr>
                                    <td class="ps-3 fw-semibold small text-primary">{{ $tx->charge_number }}</td>
                                    <td>
                                        <div class="fw-medium small">
                                            @if($tx->folio)
                                                Folio #{{ $tx->folio->folio_number }}
                                                @if($tx->folio->guest)
                                                    <br><span class="text-muted text-xs">{{ $tx->folio->guest->first_name }} {{ $tx->folio->guest->last_name }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $tx->charge_code }}</span>
                                        <span class="small text-muted">{{ $tx->chargeCode?->description }}</span>
                                    </td>
                                    <td>{{ $tx->chargeCode?->category }}</td>
                                    <td>
                                        @if($tx->payment_method !== 'NONE')
                                            <span class="badge bg-success-subtle text-success">{{ $tx->payment_method }}</span>
                                        @else
                                            <span class="text-muted small">None</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted" style="max-width: 180px;">{{ $tx->reference_notes }}</td>
                                    <td class="text-end fw-semibold text-danger">
                                        {{ $tx->charge_amount > 0 ? '₱' . number_format($tx->charge_amount, 2) : '—' }}
                                    </td>
                                    <td class="text-end fw-semibold text-success pe-3">
                                        {{ $tx->credit_amount > 0 ? '₱' . number_format($tx->credit_amount, 2) : '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        No transactions recorded during this shift.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

@if($shift)
<!-- PRINT ONLY VIEW -->
<div class="d-none d-print-block" style="font-family: Arial, sans-serif; color: #000000; background: #ffffff; padding: 20px;">
    <!-- Header Logo & Hotel Info -->
    <div class="d-flex align-items-center justify-content-center mb-4 border-bottom border-dark pb-3">
        <img src="{{ asset('images/logo.png') }}" alt="Don Felipe Hotel Logo" class="me-3" style="width: 80px; height: 80px; object-fit: contain;">
        <div class="text-center">
            <h3 class="fw-bold mb-0" style="font-family: Arial, sans-serif;">HOTEL DON FELIPE</h3>
            <small class="d-block text-muted">Bonifacio Street, Ormoc City</small>
            <small class="d-block text-muted">Tel. Nos. 255-3580 | Fax No. 561-9620</small>
            <small class="d-block text-muted">Email: hdfelipe@yahoo.com</small>
            <h5 class="fw-bold mt-2 mb-0 text-uppercase" style="letter-spacing: 1px;">Shift Sales Report</h5>
        </div>
    </div>

    <!-- Meta Details (2 Columns) -->
    <div class="row mb-4" style="font-size: 13px; font-family: Arial, sans-serif; line-height: 1.6;">
        <div class="col-6">
            <div class="d-flex mb-1">
                <span class="fw-bold" style="min-width: 130px; display: inline-block;">DATE:</span>
                <span>{{ $schedule->shift_date->format('m/d/Y') }}</span>
            </div>
            <div class="d-flex mb-1">
                <span class="fw-bold" style="min-width: 130px; display: inline-block;">EMPLOYEE NAME:</span>
                <span>{{ $schedule->user->full_name }}</span>
            </div>
            <div class="d-flex mb-1">
                <span class="fw-bold" style="min-width: 130px; display: inline-block;">DESIGNATION:</span>
                <span>{{ $schedule->user->role?->role_name ?? 'Staff' }}</span>
            </div>
        </div>
        <div class="col-6">
            <div class="d-flex mb-1">
                <span class="fw-bold" style="min-width: 150px; display: inline-block;">REPORT DATE:</span>
                <span>{{ now()->format('m/d/Y') }}</span>
            </div>
            <div class="d-flex mb-1">
                <span class="fw-bold" style="min-width: 150px; display: inline-block;">REPORT TIME:</span>
                <span>{{ now()->format('g:i A') }}</span>
            </div>
            <div class="d-flex mb-1">
                <span class="fw-bold" style="min-width: 150px; display: inline-block;">SHIFT DURATION:</span>
                <span>
                    {{ \Carbon\Carbon::parse($schedule->scheduled_start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($schedule->scheduled_end_time)->format('g:i A') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Transaction Table -->
    <table class="table print-table" style="font-size: 12px; border: 1px solid #000; border-collapse: collapse; width: 100%;">
        <thead>
            <tr style="border-bottom: 2px solid #000; background-color: #f8f9fa;">
                <th class="p-2" style="width: 15%; text-align: left; border: 1px solid #000;">DATE</th>
                <th class="p-2" style="width: 45%; text-align: left; border: 1px solid #000;">REFERENCE</th>
                <th class="p-2 text-end" style="width: 20%; border: 1px solid #000;">CHARGE</th>
                <th class="p-2 text-end" style="width: 20%; border: 1px solid #000;">CREDIT</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
                <tr style="border-bottom: 1px solid #ddd;">
                    <td class="p-2" style="border: 1px solid #000;">{{ $tx->timestamp->format('m/d/Y') }}</td>
                    <td class="p-2" style="border: 1px solid #000;">
                        {{ $tx->chargeCode?->description ?? 'Room Post' }} ({{ $tx->charge_number }})
                        @if($tx->folio && $tx->folio->guest)
                            - Guest: {{ $tx->folio->guest->first_name }} {{ $tx->folio->guest->last_name }}
                        @endif
                    </td>
                    <td class="p-2 text-end" style="border: 1px solid #000;">
                        {{ $tx->charge_amount > 0 ? number_format($tx->charge_amount, 2) : '—' }}
                    </td>
                    <td class="p-2 text-end" style="border: 1px solid #000;">
                        {{ $tx->credit_amount > 0 ? number_format($tx->credit_amount, 2) : '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center p-3" style="border: 1px solid #000;">No transactions recorded.</td>
                </tr>
            @endforelse
            <!-- Total Row -->
            <tr style="border-top: 2px solid #000; font-weight: bold; background-color: #f8f9fa;">
                <td colspan="2" class="p-2 text-end" style="border: 1px solid #000;">Total Balance - ₱</td>
                <td class="p-2 text-end" style="border: 1px solid #000;">{{ number_format($salesSummary['total_charges'], 2) }}</td>
                <td class="p-2 text-end" style="border: 1px solid #000;">{{ number_format($salesSummary['total_payments'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="text-center my-3 fw-semibold text-muted" style="font-size: 12px; font-style: italic;">
        *** Nothing follows ***
    </div>

    <!-- Summary / Category breakdown -->
    <div class="mt-4" style="font-size: 12px; max-width: 450px;">
        <h6 class="fw-bold text-uppercase border-bottom border-dark pb-1" style="font-family: Arial, sans-serif;">SUMMARY :</h6>
        <table class="table table-sm table-borderless" style="width: 100%;">
            @php
                $breakdown = [];
                foreach($transactions as $tx) {
                    if ($tx->charge_amount > 0) {
                        $desc = $tx->chargeCode?->description ?? 'ROOM SALES';
                        $breakdown[$desc] = ($breakdown[$desc] ?? 0.00) + $tx->charge_amount;
                    }
                    if ($tx->credit_amount > 0) {
                        $desc = $tx->payment_method !== 'NONE' ? $tx->payment_method . ' PAYMENT' : 'PAYMENTS';
                        $breakdown[$desc] = ($breakdown[$desc] ?? 0.00) + $tx->credit_amount;
                    }
                }
            @endphp
            @forelse($breakdown as $desc => $amt)
                <tr>
                    <td class="p-1" style="text-align: left;">{{ strtoupper($desc) }}</td>
                    <td class="p-1 text-end fw-semibold">₱{{ number_format($amt, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-muted text-center">No sales log breakdown.</td>
                </tr>
            @endforelse
            <tr style="border-top: 1px solid #000; font-weight: bold;">
                <td class="p-1" style="text-align: left;">NET BALANCE</td>
                <td class="p-1 text-end">₱{{ number_format($salesSummary['total_charges'] - $salesSummary['total_payments'], 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Remarks & Signature Lines -->
    <div class="row mt-5 pt-3" style="font-size: 12px; font-family: Arial, sans-serif;">
        <div class="col-12 mb-4">
            <p><strong>Remarks:</strong> ____________________________________________________________________________________________________</p>
        </div>
        <div class="col-6 mt-4">
            <div style="border-top: 1px solid #000; width: 220px; margin-top: 40px;" class="text-center pt-1">
                Prepared By (Employee Signature)
            </div>
        </div>
        <div class="col-6 mt-4 text-end d-flex flex-column align-items-end">
            <div style="border-top: 1px solid #000; width: 220px; margin-top: 40px;" class="text-center pt-1">
                Audited By (Manager Signature)
            </div>
        </div>
    </div>
</div>
@endif

@endsection
