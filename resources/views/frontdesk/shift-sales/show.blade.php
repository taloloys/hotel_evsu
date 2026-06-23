@extends('layouts.app')

@section('title', 'Shift #' . $shift->shift_id . ' Report - Hotel Don Felipe')
@section('pageTitle', 'Shift Sales Report')
@section('pageSubtitle', 'Shift #' . $shift->shift_id . ' — ' . ($shift->user?->full_name ?? 'Unknown Employee'))

@section('content')

<style>
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
    }
    .info-item label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6c757d;
        display: block;
        margin-bottom: 2px;
    }
    .info-item .value {
        font-weight: 600;
        font-size: 0.95rem;
    }
    .stat-badge {
        border-radius: 12px;
        padding: 1rem 1.25rem;
        flex: 1;
        min-width: 140px;
    }
    @media print {
        body { background: #fff !important; color: #000 !important; font-size: 11px !important; }
        .sidebar, .main-content > .card:first-child, .hide-on-print, .btn, .btn-group { display: none !important; }
        .main-content { margin-left: 0 !important; padding: 0 !important; }
        .container-fluid { width: 100% !important; padding: 0 !important; margin: 0 !important; }
        .card { border: none !important; box-shadow: none !important; border-radius: 0 !important; background: transparent !important; }
        .card-body { padding: 0 !important; }
        .table-responsive { overflow: visible !important; }
        .table { border: 1px solid #000 !important; width: 100% !important; }
        .table th, .table td { border: 1px solid #000 !important; padding: 4px 6px !important; }
    }
</style>

<div class="container-fluid">

    {{-- Action bar --}}
    <div class="d-flex align-items-center gap-2 mb-4 hide-on-print">
        <a href="{{ route(request()->routeIs('admin.*') ? 'admin.shift-sales' : 'frontdesk.shift-sales') }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Reports
        </a>
        <button type="button" onclick="window.print()" class="btn btn-success btn-sm px-3">
            <i class="fa-solid fa-print me-1"></i> Print / Save PDF
        </button>
    </div>

    {{-- ======================== SCREEN VIEW ======================== --}}
    <div class="d-print-none">

        {{-- Shift Info Card --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <i class="fa-solid fa-id-badge me-2 text-primary"></i>
                            Shift #{{ $shift->shift_id }}
                            @if(! $shift->end_time)
                                <span class="badge bg-success ms-2">Active</span>
                            @endif
                        </h5>
                        <p class="text-muted mb-0 small">Complete sales report for this shift session.</p>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <label>Employee Name</label>
                        <div class="value">{{ $shift->user?->full_name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <label>Employee ID</label>
                        <div class="value">#{{ $shift->user_id }}</div>
                    </div>
                    <div class="info-item">
                        <label>Designation</label>
                        <div class="value">{{ $shift->user?->role?->role_name ?? 'Staff' }}</div>
                    </div>
                    <div class="info-item">
                        <label>Shift Start</label>
                        <div class="value">{{ $shift->start_time?->format('M d, Y g:i A') ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <label>Shift End</label>
                        <div class="value">
                            @if($shift->end_time)
                                {{ $shift->end_time->format('M d, Y g:i A') }}
                            @else
                                <span class="text-success fw-bold">Ongoing</span>
                            @endif
                        </div>
                    </div>
                    @if($shift->schedule)
                        <div class="info-item">
                            <label>Schedule</label>
                            <div class="value">{{ $shift->schedule->shift_name }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Summary Stats --}}
        <div class="d-flex flex-wrap gap-3 mb-4">
            <div class="stat-badge bg-primary bg-opacity-10 border border-primary border-opacity-25">
                <div class="text-primary small fw-semibold mb-1">Room Charges</div>
                <div class="fw-bold fs-5 text-primary">₱{{ number_format($totals['room_charges'], 2) }}</div>
            </div>
            <div class="stat-badge bg-warning bg-opacity-10 border border-warning border-opacity-25">
                <div class="text-warning small fw-semibold mb-1">Additional Charges</div>
                <div class="fw-bold fs-5 text-warning">₱{{ number_format($totals['additional_charges'], 2) }}</div>
            </div>
            <div class="stat-badge bg-success bg-opacity-10 border border-success border-opacity-25">
                <div class="text-success small fw-semibold mb-1">Payments Collected</div>
                <div class="fw-bold fs-5 text-success">₱{{ number_format($totals['payments'], 2) }}</div>
            </div>
            <div class="stat-badge bg-info bg-opacity-10 border border-info border-opacity-25">
                <div class="text-info small fw-semibold mb-1">Rooms Billed</div>
                <div class="fw-bold fs-5 text-info">{{ $totals['checkin_count'] }}</div>
            </div>
            <div class="stat-badge {{ $totals['net_income'] >= 0 ? 'bg-dark bg-opacity-10 border border-dark border-opacity-25' : 'bg-danger bg-opacity-10 border border-danger border-opacity-25' }}">
                <div class="{{ $totals['net_income'] >= 0 ? 'text-dark' : 'text-danger' }} small fw-semibold mb-1">Net Balance</div>
                <div class="fw-bold fs-5 {{ $totals['net_income'] >= 0 ? '' : 'text-danger' }}">
                    ₱{{ number_format(abs($totals['net_income']), 2) }}
                    @if($totals['net_income'] < 0) <small class="fs-6">(CR)</small> @endif
                </div>
            </div>
        </div>

        {{-- Transaction Table --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-receipt me-2 text-muted"></i> Transaction Detail
                </h5>
                <span class="badge bg-secondary">{{ $transactions->count() }} records</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Doc No.</th>
                                <th>Date / Time</th>
                                <th>Folio</th>
                                <th>Guest</th>
                                <th>Charge Code</th>
                                <th>Payment</th>
                                <th class="text-end">Charge</th>
                                <th class="text-end pe-4">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $tx)
                                <tr>
                                    <td class="ps-4 fw-semibold small text-primary">{{ $tx->charge_number ?? '—' }}</td>
                                    <td class="small text-nowrap">{{ $tx->timestamp?->format('M d, Y g:i A') }}</td>
                                    <td class="small fw-medium">{{ $tx->folio?->folio_number ?? '—' }}</td>
                                    <td class="small">
                                        @if($tx->folio?->guest)
                                            {{ $tx->folio->guest->first_name }} {{ $tx->folio->guest->last_name }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $tx->charge_code }}</span>
                                        <span class="small text-muted ms-1">{{ $tx->chargeCode?->description }}</span>
                                    </td>
                                    <td>
                                        @if($tx->payment_method !== 'NONE')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                {{ $tx->payment_method }}
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold text-danger">
                                        {{ $tx->charge_amount > 0 ? '₱' . number_format($tx->charge_amount, 2) : '—' }}
                                    </td>
                                    <td class="text-end fw-semibold text-success pe-4">
                                        {{ $tx->credit_amount > 0 ? '₱' . number_format($tx->credit_amount, 2) : '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-inbox fa-2x mb-2 d-block opacity-30"></i>
                                        No transactions recorded for this shift.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($transactions->isNotEmpty())
                            <tfoot class="table-secondary fw-bold">
                                <tr>
                                    <td colspan="6" class="ps-4 text-end">Totals</td>
                                    <td class="text-end text-danger">₱{{ number_format($totals['total_charges'], 2) }}</td>
                                    <td class="text-end text-success pe-4">₱{{ number_format($totals['payments'], 2) }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

    </div>{{-- end d-print-none --}}

    {{-- ======================== PRINT LAYOUT ======================== --}}
    <div class="d-none d-print-block" style="font-family: Arial, sans-serif; color: #000; background: #fff; padding: 20px;">

        {{-- Header --}}
        <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 12px;">
            <img src="{{ asset('images/logo.png') }}" alt="Hotel Don Felipe" style="width: 70px; height: 70px; object-fit: contain; margin-right: 16px;">
            <div style="text-align: center;">
                <div style="font-size: 18px; font-weight: bold; letter-spacing: 1px;">HOTEL DON FELIPE</div>
                <div style="font-size: 11px; color: #444;">Bonifacio Street, Ormoc City</div>
                <div style="font-size: 11px; color: #444;">Tel. 255-3580 | Fax. 561-9620 | hdfelipe@yahoo.com</div>
                <div style="font-size: 14px; font-weight: bold; margin-top: 6px; letter-spacing: 1px;">SHIFT SALES REPORT — Shift #{{ $shift->shift_id }}</div>
            </div>
        </div>

        {{-- Employee & Shift Info --}}
        <div style="display: flex; gap: 40px; font-size: 12px; margin-bottom: 16px; line-height: 1.9;">
            <div>
                <div><strong style="display: inline-block; min-width: 140px;">EMPLOYEE NAME:</strong> {{ $shift->user?->full_name ?? 'N/A' }}</div>
                <div><strong style="display: inline-block; min-width: 140px;">EMPLOYEE ID:</strong> #{{ $shift->user_id }}</div>
                <div><strong style="display: inline-block; min-width: 140px;">DESIGNATION:</strong> {{ $shift->user?->role?->role_name ?? 'Staff' }}</div>
            </div>
            <div>
                <div><strong style="display: inline-block; min-width: 140px;">SHIFT DATE:</strong> {{ $shift->start_time?->format('m/d/Y') ?? 'N/A' }}</div>
                <div><strong style="display: inline-block; min-width: 140px;">SHIFT START:</strong> {{ $shift->start_time?->format('g:i A') ?? 'N/A' }}</div>
                <div><strong style="display: inline-block; min-width: 140px;">SHIFT END:</strong> {{ $shift->end_time?->format('g:i A') ?? 'Ongoing' }}</div>
            </div>
            <div>
                <div><strong style="display: inline-block; min-width: 130px;">REPORT DATE:</strong> {{ now()->format('m/d/Y') }}</div>
                <div><strong style="display: inline-block; min-width: 130px;">REPORT TIME:</strong> {{ now()->format('g:i A') }}</div>
            </div>
        </div>

        {{-- Summary Grid --}}
        <table style="width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 16px;">
            <tr style="background: #f0f0f0;">
                <th style="border: 1px solid #000; padding: 6px 10px; width: 20%;">Rooms Billed</th>
                <th style="border: 1px solid #000; padding: 6px 10px; width: 20%;">Room Charges</th>
                <th style="border: 1px solid #000; padding: 6px 10px; width: 20%;">Additional Charges</th>
                <th style="border: 1px solid #000; padding: 6px 10px; width: 20%;">Payments Collected</th>
                <th style="border: 1px solid #000; padding: 6px 10px; width: 20%;">Net Balance</th>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 6px 10px; text-align: center; font-size: 16px; font-weight: bold;">{{ $totals['checkin_count'] }}</td>
                <td style="border: 1px solid #000; padding: 6px 10px; text-align: right; font-weight: bold;">₱{{ number_format($totals['room_charges'], 2) }}</td>
                <td style="border: 1px solid #000; padding: 6px 10px; text-align: right; font-weight: bold;">₱{{ number_format($totals['additional_charges'], 2) }}</td>
                <td style="border: 1px solid #000; padding: 6px 10px; text-align: right; font-weight: bold;">₱{{ number_format($totals['payments'], 2) }}</td>
                <td style="border: 1px solid #000; padding: 6px 10px; text-align: right; font-weight: bold;">
                    ₱{{ number_format(abs($totals['net_income']), 2) }}{{ $totals['net_income'] < 0 ? ' CR' : '' }}
                </td>
            </tr>
        </table>

        {{-- Transaction Table --}}
        <table style="width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 16px;">
            <thead>
                <tr style="background: #f0f0f0;">
                    <th style="border: 1px solid #000; padding: 5px 6px; text-align: left; width: 10%;">DATE</th>
                    <th style="border: 1px solid #000; padding: 5px 6px; text-align: left; width: 12%;">DOC NO.</th>
                    <th style="border: 1px solid #000; padding: 5px 6px; text-align: left; width: 10%;">FOLIO</th>
                    <th style="border: 1px solid #000; padding: 5px 6px; text-align: left; width: 33%;">DESCRIPTION / GUEST</th>
                    <th style="border: 1px solid #000; padding: 5px 6px; text-align: left; width: 10%;">PAYMENT</th>
                    <th style="border: 1px solid #000; padding: 5px 6px; text-align: right; width: 12%;">CHARGE</th>
                    <th style="border: 1px solid #000; padding: 5px 6px; text-align: right; width: 13%;">CREDIT</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px 6px;">{{ $tx->timestamp?->format('m/d/Y') }}</td>
                        <td style="border: 1px solid #000; padding: 4px 6px; font-size: 10px;">{{ $tx->charge_number ?? '—' }}</td>
                        <td style="border: 1px solid #000; padding: 4px 6px;">{{ $tx->folio?->folio_number ?? '—' }}</td>
                        <td style="border: 1px solid #000; padding: 4px 6px;">
                            {{ $tx->chargeCode?->description ?? 'Charge' }}
                            @if($tx->folio?->guest)
                                — {{ $tx->folio->guest->first_name }} {{ $tx->folio->guest->last_name }}
                            @endif
                        </td>
                        <td style="border: 1px solid #000; padding: 4px 6px; font-size: 10px;">
                            {{ $tx->payment_method !== 'NONE' ? $tx->payment_method : '—' }}
                        </td>
                        <td style="border: 1px solid #000; padding: 4px 6px; text-align: right;">
                            {{ $tx->charge_amount > 0 ? number_format($tx->charge_amount, 2) : '—' }}
                        </td>
                        <td style="border: 1px solid #000; padding: 4px 6px; text-align: right;">
                            {{ $tx->credit_amount > 0 ? number_format($tx->credit_amount, 2) : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="border: 1px solid #000; padding: 10px; text-align: center;">No transactions recorded for this shift.</td>
                    </tr>
                @endforelse
                <tr style="background: #f0f0f0; font-weight: bold;">
                    <td colspan="5" style="border: 1px solid #000; padding: 5px 6px; text-align: right;">TOTALS</td>
                    <td style="border: 1px solid #000; padding: 5px 6px; text-align: right;">{{ number_format($totals['total_charges'], 2) }}</td>
                    <td style="border: 1px solid #000; padding: 5px 6px; text-align: right;">{{ number_format($totals['payments'], 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div style="text-align: center; font-size: 11px; font-style: italic; margin: 12px 0; color: #555;">
            *** Nothing follows ***
        </div>

        {{-- Breakdown Summary --}}
        <div style="max-width: 380px; margin-top: 16px;">
            <div style="font-weight: bold; font-size: 12px; border-bottom: 2px solid #000; padding-bottom: 4px; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;">Summary</div>
            @php
                $breakdown = [];
                foreach ($transactions as $tx) {
                    if ($tx->charge_amount > 0) {
                        $desc = strtoupper($tx->chargeCode?->description ?? 'ROOM SALES');
                        $breakdown[$desc] = ($breakdown[$desc] ?? 0.00) + $tx->charge_amount;
                    }
                    if ($tx->credit_amount > 0) {
                        $desc = $tx->payment_method !== 'NONE' ? strtoupper($tx->payment_method) . ' PAYMENT' : 'PAYMENTS';
                        $breakdown[$desc] = ($breakdown[$desc] ?? 0.00) + $tx->credit_amount;
                    }
                }
            @endphp
            <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
                @forelse($breakdown as $desc => $amt)
                    <tr>
                        <td style="padding: 3px 0;">{{ $desc }}</td>
                        <td style="text-align: right; font-weight: bold; padding: 3px 0;">₱{{ number_format($amt, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" style="color: #666;">No breakdown available.</td></tr>
                @endforelse
                <tr style="border-top: 2px solid #000; font-weight: bold;">
                    <td style="padding: 5px 0;">NET BALANCE</td>
                    <td style="text-align: right; padding: 5px 0;">
                        ₱{{ number_format(abs($totals['net_income']), 2) }}{{ $totals['net_income'] < 0 ? ' CR' : '' }}
                    </td>
                </tr>
            </table>
        </div>

        {{-- Signatures --}}
        <div style="display: flex; justify-content: space-between; margin-top: 50px; font-size: 12px;">
            <div style="text-align: center;">
                <div style="border-top: 1px solid #000; width: 220px; padding-top: 4px; margin-top: 40px;">
                    Prepared By (Employee Signature)
                </div>
            </div>
            <div style="text-align: center;">
                <div style="border-top: 1px solid #000; width: 220px; padding-top: 4px; margin-top: 40px;">
                    Audited By (Manager Signature)
                </div>
            </div>
        </div>

    </div>{{-- end d-none d-print-block --}}

</div>

@endsection
