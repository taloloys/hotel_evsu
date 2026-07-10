@extends('layouts.app')

@section('title', 'Shift Sales Report - Hotel Don Felipe')
@section('pageTitle', 'Shift Sales Report')
@section('pageSubtitle', 'Generate and print your shift sales summary.')

@section('content')

<style>
    .shift-card {
        transition: all 0.2s ease;
        cursor: pointer;
        border: 2px solid transparent;
    }
    .shift-card:hover {
        border-color: var(--bs-primary);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12) !important;
    }
    .shift-card.active-shift {
        border-color: #198754;
        background: linear-gradient(135deg, #d1e7dd 0%, #f0faf5 100%) !important;
    }
    .stat-card {
        border-radius: 16px;
        transition: all 0.2s ease;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
    }
    .mode-tab {
        border-radius: 10px;
        font-weight: 600;
        padding: 0.5rem 1.25rem;
        border: 2px solid #dee2e6;
        background: transparent;
        color: #6c757d;
        transition: all 0.15s;
    }
    .mode-tab.active {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }
    .mode-tab:hover:not(.active) {
        border-color: #0d6efd;
        color: #0d6efd;
    }
    .filter-panel { display: none; }
    .filter-panel.active { display: block; }

    @media print {
        body { background: #fff !important; color: #000 !important; font-size: 11px !important; }
        .sidebar, .main-content > .card:first-child, .hide-on-print, .btn, .btn-group, nav { display: none !important; }
        .main-content { margin-left: 0 !important; padding: 0 !important; }
        .container-fluid { width: 100% !important; padding: 0 !important; margin: 0 !important; }
        .card { border: none !important; box-shadow: none !important; border-radius: 0 !important; margin-bottom: 16px !important; background: transparent !important; }
        .card-body { padding: 0 !important; }
        .table-responsive { overflow: visible !important; }
        .table { border: 1px solid #000 !important; width: 100% !important; margin-bottom: 16px !important; }
        .table th, .table td { border: 1px solid #000 !important; padding: 4px 6px !important; }
        .stat-card { border: 1px solid #000 !important; background: #f8f9fa !important; color: #000 !important; border-radius: 0 !important; padding: 8px 12px !important; }
        .stat-card * { color: #000 !important; }
    }
</style>

<div class="container-fluid d-print-none">
    <div class="row justify-content-center">

        {{-- ======================================================= --}}
        {{-- FILTER PANEL --}}
        {{-- ======================================================= --}}
        <div class="col-lg-12 mb-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h4 class="fw-bold mb-0">
                            <i class="fa-solid fa-file-chart-column me-2 text-primary"></i>
                            Shift Sales Report Criteria
                        </h4>
                        @if($activeShift)
                            <a href="{{ route(request()->routeIs('admin.*') ? 'admin.shift-sales' : 'frontdesk.shift-sales', ['shift_id' => $activeShift->shift_id]) }}"
                               class="btn btn-success btn-sm px-3">
                                <i class="fa-solid fa-circle-dot fa-beat me-1"></i>
                                My Active Shift
                            </a>
                        @endif
                    </div>

                    {{-- Filter Mode Tabs --}}
                    @if($isAdmin)
                    <div class="d-flex gap-2 mb-4" id="modeTabs">
                        <button type="button" class="mode-tab {{ !isset($filters['shift_id']) ? 'active' : '' }}" onclick="switchMode('date')">
                            <i class="fa-solid fa-calendar-range me-1"></i> By Date Range
                        </button>
                        <button type="button" class="mode-tab {{ isset($filters['shift_id']) ? 'active' : '' }}" onclick="switchMode('shift')">
                            <i class="fa-solid fa-clock-rotate-left me-1"></i> By Shift
                        </button>
                    </div>
                    @endif

                    <form action="{{ request()->routeIs('admin.*') ? route('admin.shift-sales') : route('frontdesk.shift-sales') }}" method="GET" id="reportForm">

                        {{-- ============ DATE RANGE MODE ============ --}}
                        <div id="datePanel" class="filter-panel {{ !$isAdmin || !isset($filters['shift_id']) ? 'active' : '' }}">

                            {{-- Charge Code Range --}}
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Charge Code Range</label>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" name="charge_code_from">
                                        <option value="">From (Any)</option>
                                        @foreach($chargeCodes as $code)
                                            <option value="{{ $code->charge_code }}"
                                                {{ (isset($filters['charge_code_from']) && $filters['charge_code_from'] == $code->charge_code) ? 'selected' : '' }}>
                                                [{{ $code->charge_code }}] {{ $code->description }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1 text-center text-muted">to</div>
                                <div class="col-md-4">
                                    <select class="form-select" name="charge_code_until">
                                        <option value="">To (Any)</option>
                                        @foreach($chargeCodes as $code)
                                            <option value="{{ $code->charge_code }}"
                                                {{ (isset($filters['charge_code_until']) && $filters['charge_code_until'] == $code->charge_code) ? 'selected' : '' }}>
                                                [{{ $code->charge_code }}] {{ $code->description }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Transaction Dates --}}
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Transaction Dates</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="date" class="form-control" name="date_from"
                                        value="{{ !empty($filters['date_from']) ? $filters['date_from'] : now()->toDateString() }}">
                                </div>
                                <div class="col-md-1 text-center text-muted">to</div>
                                <div class="col-md-4">
                                    <input type="date" class="form-control" name="date_until"
                                        value="{{ !empty($filters['date_until']) ? $filters['date_until'] : now()->toDateString() }}">
                                </div>
                            </div>

                            {{-- Employee --}}
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Employee / Cashier</label>
                                </div>
                                <div class="col-md-9">
                                    @if($isAdmin)
                                        <select class="form-select" name="employee_id" id="employeeSelect">
                                            <option value="">All Employees</option>
                                            @foreach($users as $u)
                                                <option value="{{ $u->user_id }}"
                                                    {{ (isset($filters['employee_id']) && $filters['employee_id'] == $u->user_id) ? 'selected' : '' }}>
                                                    {{ $u->full_name }} ({{ $u->role?->role_name ?? 'No Role' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="hidden" name="employee_id" value="{{ auth()->id() }}">
                                        <input type="text" class="form-control" value="{{ auth()->user()->full_name }}" disabled>
                                    @endif
                                </div>
                            </div>

                            {{-- Report Type --}}
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Category</label>
                                </div>
                                <div class="col-md-9 d-flex gap-3 flex-wrap">
                                    @foreach(['hotel' => 'Hotel Charges', 'restaurant' => 'Restaurant / Coffee Shop', 'all' => 'All Transactions'] as $val => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="report_type"
                                                id="type_{{ $val }}" value="{{ $val }}"
                                                {{ (!isset($filters['report_type']) && $val === 'hotel') || (isset($filters['report_type']) && $filters['report_type'] === $val) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="type_{{ $val }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- ============ SHIFT SELECTOR MODE ============ --}}
                        @if($isAdmin)
                        <div id="shiftPanel" class="filter-panel {{ isset($filters['shift_id']) ? 'active' : '' }}">
                            <div class="row mb-2 align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Select Shift</label>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-select" name="shift_id" id="shiftSelect">
                                        <option value="">— Choose a shift —</option>
                                        @foreach($shiftsForSelector as $s)
                                            <option value="{{ $s->shift_id }}"
                                                {{ (isset($filters['shift_id']) && $filters['shift_id'] == $s->shift_id) ? 'selected' : '' }}>
                                                @if(! $s->end_time)
                                                    🟢 [ACTIVE]
                                                @endif
                                                Shift #{{ $s->shift_id }}
                                                {{ $isAdmin ? ' — ' . $s->user?->full_name : '' }}
                                                | {{ $s->start_time?->format('M d, Y g:i A') }}
                                                @if($s->end_time)
                                                    – {{ $s->end_time->format('g:i A') }}
                                                @else
                                                    (ongoing)
                                                @endif
                                                @if($s->schedule)
                                                    | {{ $s->schedule->shift_name }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @if($shiftsForSelector->isEmpty())
                                <div class="alert alert-info mt-2">
                                    <i class="fa-solid fa-circle-info me-1"></i>
                                    No shifts found. Start a shift from the dashboard to begin tracking transactions.
                                </div>
                            @endif
                        </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="d-flex gap-2 justify-content-end mt-4 hide-on-print">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Generate Report
                            </button>
                            @if($hasSearched && $transactions->isNotEmpty())
                                <button type="button" onclick="window.print()" class="btn btn-success px-4">
                                    <i class="fa-solid fa-print me-1"></i> Print / Save PDF
                                </button>
                            @endif
                            @if($hasSearched)
                                <a href="{{ request()->routeIs('admin.*') ? route('admin.shift-sales') : route('frontdesk.shift-sales') }}"
                                   class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-xmark me-1"></i> Clear
                                </a>
                            @endif
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- ======================================================= --}}
        {{-- REPORT RESULTS --}}
        {{-- ======================================================= --}}
        @if($hasSearched)

            {{-- Shift Info Banner (when viewing a specific shift) --}}
            @if($selectedShift)
                <div class="col-lg-12 mb-3">
                    <div class="card border-0 shadow-sm rounded-4 bg-dark text-white">
                        <div class="card-body px-4 py-3 d-flex flex-wrap align-items-center gap-4">
                            <div>
                                <div class="text-white-50 small">Employee</div>
                                <div class="fw-bold fs-6">{{ $selectedShift->user?->full_name }}</div>
                            </div>
                            <div>
                                <div class="text-white-50 small">Employee ID</div>
                                <div class="fw-bold">#{{ $selectedShift->user_id }}</div>
                            </div>
                            <div>
                                <div class="text-white-50 small">Shift #</div>
                                <div class="fw-bold">#{{ $selectedShift->shift_id }}</div>
                            </div>
                            <div>
                                <div class="text-white-50 small">Shift Start</div>
                                <div class="fw-bold">{{ $selectedShift->start_time?->format('M d, Y g:i A') }}</div>
                            </div>
                            <div>
                                <div class="text-white-50 small">Shift End</div>
                                <div class="fw-bold">
                                    @if($selectedShift->end_time)
                                        {{ $selectedShift->end_time->format('M d, Y g:i A') }}
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                </div>
                            </div>
                            @if($selectedShift->schedule)
                                <div>
                                    <div class="text-white-50 small">Schedule</div>
                                    <div class="fw-bold">{{ $selectedShift->schedule->shift_name }}</div>
                                </div>
                            @endif
                            <div class="ms-auto">
                                <a href="{{ route(request()->routeIs('admin.*') ? 'admin.shift-sales.show' : 'frontdesk.shift-sales.show', $selectedShift->shift_id) }}"
                                   class="btn btn-outline-light btn-sm" target="_blank">
                                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Full Report View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Summary Cards --}}
            <div class="col-lg-12 mb-4">
                <div class="row g-3">
                    <div class="col-md col-6">
                        <div class="card stat-card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                        <i class="fa-solid fa-bed"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Room Charges</div>
                                        <div class="fw-bold fs-5 text-primary">₱{{ number_format($totals['room_charges'], 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md col-6">
                        <div class="card stat-card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                        <i class="fa-solid fa-utensils"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Additional Charges</div>
                                        <div class="fw-bold fs-5 text-warning">₱{{ number_format($totals['additional_charges'], 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md col-6">
                        <div class="card stat-card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                                        <i class="fa-solid fa-peso-sign"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Payments Collected</div>
                                        <div class="fw-bold fs-5 text-success">₱{{ number_format($totals['payments'], 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md col-6">
                        <div class="card stat-card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                                        <i class="fa-solid fa-door-open"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Rooms Billed</div>
                                        <div class="fw-bold fs-5 text-info">{{ $totals['checkin_count'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md col-6">
                        <div class="card stat-card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                                        <i class="fa-solid fa-money-bill-transfer"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Expenses</div>
                                        <div class="fw-bold fs-5 text-danger">₱{{ number_format($totals['shift_expenses'], 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md col-6">
                        <div class="card stat-card border-0 shadow-sm h-100 {{ $totals['net_income'] >= 0 ? 'border-success' : 'border-danger' }}" style="border-width: 2px !important;">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stat-icon {{ $totals['net_income'] >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}">
                                        <i class="fa-solid fa-chart-line"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Net Balance</div>
                                        <div class="fw-bold fs-5 {{ $totals['net_income'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            ₱{{ number_format(abs($totals['net_income']), 2) }}
                                            @if($totals['net_income'] < 0) <small>(CR)</small> @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Transaction Table --}}
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold mb-0">
                            <i class="fa-solid fa-receipt me-2 text-muted"></i>
                            Transaction Audit
                        </h5>
                        <span class="badge bg-secondary">{{ $transactions->count() }} transaction{{ $transactions->count() !== 1 ? 's' : '' }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="transactionsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Doc No.</th>
                                        <th>Date / Time</th>
                                        <th>Folio</th>
                                        <th>Guest</th>
                                        <th>Employee</th>
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
                                            <td class="small">{{ $tx->user?->full_name ?? '—' }}</td>
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
                                            <td colspan="9" class="text-center py-5 text-muted">
                                                <i class="fa-solid fa-inbox fa-2x mb-2 d-block opacity-30"></i>
                                                No transactions found for the selected criteria.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($transactions->isNotEmpty())
                                    <tfoot class="table-secondary fw-bold">
                                        <tr>
                                            <td colspan="7" class="ps-4 text-end">Totals</td>
                                            <td class="text-end text-danger">₱{{ number_format($totals['total_charges'], 2) }}</td>
                                            <td class="text-end text-success pe-4">₱{{ number_format($totals['payments'], 2) }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        @endif

    </div>
</div>

{{-- ======================================================= --}}
{{-- PRINT LAYOUT --}}
{{-- ======================================================= --}}
@if($hasSearched)
<div class="d-none d-print-block" style="font-family: Arial, sans-serif; color: #000; background: #fff; padding: 20px;">

    {{-- Header --}}
    <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 12px;">
        <img src="{{ asset('images/logo.png') }}" alt="Hotel Don Felipe" style="width: 70px; height: 70px; object-fit: contain; margin-right: 16px;">
        <div style="text-align: center;">
            <div style="font-size: 18px; font-weight: bold; letter-spacing: 1px;">HOTEL DON FELIPE</div>
            <div style="font-size: 11px; color: #444;">Bonifacio Street, Ormoc City</div>
            <div style="font-size: 11px; color: #444;">Tel. 255-3580 | Fax. 561-9620 | hdfelipe@yahoo.com</div>
            <div style="font-size: 14px; font-weight: bold; margin-top: 6px; letter-spacing: 1px;">SHIFT SALES REPORT</div>
        </div>
    </div>

    {{-- Report Meta --}}
    @php
        $reportEmployee = null;
        $reportShift = $selectedShift;
        if (!$isAdmin) {
            $reportEmployee = auth()->user();
        } elseif ($selectedShift) {
            $reportEmployee = $selectedShift->user;
        } elseif (isset($filters['employee_id']) && $filters['employee_id']) {
            $reportEmployee = \App\Models\User::with('role')->find($filters['employee_id']);
        } else {
            $reportEmployee = auth()->user();
        }
    @endphp

    <div style="display: flex; gap: 40px; font-size: 12px; margin-bottom: 16px; line-height: 1.8;">
        <div>
            <div><strong style="display: inline-block; min-width: 130px;">EMPLOYEE NAME:</strong> {{ $reportEmployee?->full_name ?? 'N/A' }}</div>
            <div><strong style="display: inline-block; min-width: 130px;">EMPLOYEE ID:</strong> #{{ $reportEmployee?->user_id ?? 'N/A' }}</div>
            <div><strong style="display: inline-block; min-width: 130px;">DESIGNATION:</strong> {{ $reportEmployee?->role?->role_name ?? 'Staff' }}</div>
        </div>
        <div>
            <div>
                <strong style="display: inline-block; min-width: 130px;">SHIFT DATE:</strong>
                @if($reportShift)
                    {{ $reportShift->start_time?->format('m/d/Y') }}
                @elseif(isset($filters['date_from']) && $filters['date_from'])
                    {{ \Carbon\Carbon::parse($filters['date_from'])->format('m/d/Y') }}
                    @if(isset($filters['date_until']) && $filters['date_until'])
                        – {{ \Carbon\Carbon::parse($filters['date_until'])->format('m/d/Y') }}
                    @endif
                @else
                    {{ now()->format('m/d/Y') }}
                @endif
            </div>
            <div>
                <strong style="display: inline-block; min-width: 130px;">SHIFT START:</strong>
                {{ $reportShift?->start_time?->format('g:i A') ?? 'N/A' }}
            </div>
            <div>
                <strong style="display: inline-block; min-width: 130px;">SHIFT END:</strong>
                {{ $reportShift?->end_time?->format('g:i A') ?? 'Ongoing' }}
            </div>
        </div>
        <div>
            <div><strong style="display: inline-block; min-width: 130px;">REPORT DATE:</strong> {{ now()->format('m/d/Y') }}</div>
            <div><strong style="display: inline-block; min-width: 130px;">REPORT TIME:</strong> {{ now()->format('g:i A') }}</div>
        </div>
    </div>

    {{-- Summary Grid --}}
    <table style="width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 16px;">
        <tr style="background: #f0f0f0;">
            <th style="border: 1px solid #000; background-color: #f8f9fa; padding: 6px 10px; text-align: left;">C/I</th>
            <th style="border: 1px solid #000; background-color: #f8f9fa; padding: 6px 10px; text-align: right;">RM CHG</th>
            <th style="border: 1px solid #000; background-color: #f8f9fa; padding: 6px 10px; text-align: right;">ADDL CHG</th>
            <th style="border: 1px solid #000; background-color: #f8f9fa; padding: 6px 10px; text-align: right;">PAYMENT</th>
            <th style="border: 1px solid #000; background-color: #f8f9fa; padding: 6px 10px; text-align: right; color: red;">EXPENSES</th>
            <th style="border: 1px solid #000; background-color: #f8f9fa; padding: 6px 10px; text-align: right;">NET INCM</th>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 6px 10px; text-align: center; font-size: 16px; font-weight: bold;">{{ $totals['checkin_count'] }}</td>
            <td style="border: 1px solid #000; padding: 6px 10px; text-align: right; font-weight: bold;">₱{{ number_format($totals['room_charges'], 2) }}</td>
            <td style="border: 1px solid #000; padding: 6px 10px; text-align: right; font-weight: bold;">₱{{ number_format($totals['additional_charges'], 2) }}</td>
            <td style="border: 1px solid #000; padding: 6px 10px; text-align: right; font-weight: bold;">₱{{ number_format($totals['payments'], 2) }}</td>
            <td style="border: 1px solid #000; padding: 6px 10px; text-align: right; font-weight: bold; color: red;">₱{{ number_format($totals['shift_expenses'], 2) }}</td>
            <td style="border: 1px solid #000; padding: 6px 10px; text-align: right; font-weight: bold;">₱{{ number_format(abs($totals['net_income']), 2) }}{{ $totals['net_income'] < 0 ? ' CR' : '' }}</td>
        </tr>
    </table>

    {{-- Transaction Table --}}
    <table style="width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 16px;">
        <thead>
            <tr style="background: #f0f0f0;">
                <th style="border: 1px solid #000; padding: 5px 6px; text-align: left; width: 12%;">DATE</th>
                <th style="border: 1px solid #000; padding: 5px 6px; text-align: left; width: 10%;">DOC NO.</th>
                <th style="border: 1px solid #000; padding: 5px 6px; text-align: left; width: 10%;">FOLIO</th>
                <th style="border: 1px solid #000; padding: 5px 6px; text-align: left; width: 28%;">DESCRIPTION / GUEST</th>
                <th style="border: 1px solid #000; padding: 5px 6px; text-align: left; width: 15%;">EMPLOYEE</th>
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
                    <td style="border: 1px solid #000; padding: 4px 6px; font-size: 10px;">{{ $tx->user?->full_name ?? '—' }}</td>
                    <td style="border: 1px solid #000; padding: 4px 6px; text-align: right;">
                        {{ $tx->charge_amount > 0 ? number_format($tx->charge_amount, 2) : '—' }}
                    </td>
                    <td style="border: 1px solid #000; padding: 4px 6px; text-align: right;">
                        {{ $tx->credit_amount > 0 ? number_format($tx->credit_amount, 2) : '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="border: 1px solid #000; padding: 10px; text-align: center;">No transactions found.</td>
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
                <tr><td colspan="2" style="color: #666; text-align: center;">No breakdown available.</td></tr>
            @endforelse
            <tr style="border-top: 2px solid #000; font-weight: bold; margin-top: 4px;">
                <td style="padding: 5px 0;">NET BALANCE</td>
                <td style="text-align: right; padding: 5px 0;">₱{{ number_format(abs($totals['net_income']), 2) }}{{ $totals['net_income'] < 0 ? ' CR' : '' }}</td>
            </tr>
        </table>
    </div>

    {{-- Signatures --}}
    <div style="display: flex; justify-content: space-between; margin-top: 50px; font-size: 12px;">
        <div style="text-align: center;">
            <div style="border-top: 1px solid #000; width: 200px; padding-top: 4px;">Prepared By (Employee Signature)</div>
        </div>
        <div style="text-align: center;">
            <div style="border-top: 1px solid #000; width: 200px; padding-top: 4px;">Audited By (Manager Signature)</div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    const datePanel = document.getElementById('datePanel');
    const shiftPanel = document.getElementById('shiftPanel');
    const tabs = document.querySelectorAll('.mode-tab');

    function switchMode(mode) {
        const isDate = mode === 'date';
        if (datePanel) {
            datePanel.classList.toggle('active', isDate);
        }
        if (shiftPanel) {
            shiftPanel.classList.toggle('active', !isDate);
        }
        if (tabs.length) {
            tabs.forEach((t, i) => t.classList.toggle('active', (i === 0) === isDate));
        }

        // Clear fields from the inactive panel
        if (isDate) {
            const sel = document.getElementById('shiftSelect');
            if (sel) sel.value = '';
        } else {
            document.querySelectorAll('#datePanel input, #datePanel select').forEach(el => el.value = '');
        }
    }
</script>
@endpush

@endsection