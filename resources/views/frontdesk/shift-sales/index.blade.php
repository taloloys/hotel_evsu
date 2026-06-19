@extends('layouts.app')

@section('title', 'Shift Sales - Don Felipe Hotel')
@section('pageTitle', 'Shift Sales')
@section('pageSubtitle', 'Generate and view shift sales reports.')

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
        .card.bg-primary h3, .card.bg-success h3 {
            color: #000000 !important;
            font-size: 18px !important;
        }
    }
</style>

<div class="container-fluid d-print-none">

    <div class="row justify-content-center">

        <!-- FILTERS CARD -->
        <div class="col-lg-12 mb-4">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body p-4">

                    <h4 class="fw-bold mb-4">
                        Shift Detail Report Criteria
                    </h4>

                    <form action="{{ request()->routeIs('admin.*') ? route('admin.shift-sales') : route('frontdesk.shift-sales') }}" method="GET">

                        <!-- Hotel Charge Codes -->
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    Hotel Charge Codes Range
                                </label>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" name="charge_code_from">
                                    <option value="">From (Select Code)</option>
                                    @foreach($chargeCodes as $code)
                                        <option value="{{ $code->charge_code }}" {{ (isset($filters['charge_code_from']) && $filters['charge_code_from'] == $code->charge_code) ? 'selected' : '' }}>
                                            [{{ $code->charge_code }}] {{ $code->description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1 text-center">
                                Until
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" name="charge_code_until">
                                    <option value="">To (Select Code)</option>
                                    @foreach($chargeCodes as $code)
                                        <option value="{{ $code->charge_code }}" {{ (isset($filters['charge_code_until']) && $filters['charge_code_until'] == $code->charge_code) ? 'selected' : '' }}>
                                            [{{ $code->charge_code }}] {{ $code->description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Transaction Dates -->
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    Transaction Dates
                                </label>
                            </div>
                            <div class="col-md-4">
                                <input
                                    type="date"
                                    class="form-control"
                                    name="date_from"
                                    value="{{ $filters['date_from'] ?? '' }}"
                                    required>
                            </div>
                            <div class="col-md-1 text-center">
                                Until
                            </div>
                            <div class="col-md-4">
                                <input
                                    type="date"
                                    class="form-control"
                                    name="date_until"
                                    value="{{ $filters['date_until'] ?? '' }}">
                                    @if(request()->routeIs('admin.*'))
                                        <input type="hidden" name="admin_view" value="1">
                                    @endif
                            </div>
                        </div>

                        <!-- Employee ID -->
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    Employee / Cashier
                                </label>
                            </div>
                            <div class="col-md-9">
                                @php
                                    $isAdmin = auth()->user()?->role?->role_name === 'ADMIN';
                                @endphp
                                @if($isAdmin)
                                    <select class="form-select" name="employee_id">
                                        <option value="">All Employees</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->user_id }}" {{ (isset($filters['employee_id']) && $filters['employee_id'] == $u->user_id) ? 'selected' : '' }}>
                                                {{ $u->full_name }} ({{ $u->role?->role_name ?? 'No Role' }})
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <select class="form-select" name="employee_id" style="pointer-events: none; background-color: #e9ecef;" tabindex="-1">
                                        @foreach($users as $u)
                                            <option value="{{ $u->user_id }}" selected>
                                                {{ $u->full_name }} ({{ $u->role?->role_name ?? 'No Role' }})
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>

                        <!-- Report Type -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    Report Type / Category
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="report_type"
                                        id="type_hotel"
                                        value="hotel"
                                        {{ (!isset($filters['report_type']) || $filters['report_type'] === 'hotel') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_hotel">
                                        Hotel Charges
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="report_type"
                                        id="type_restaurant"
                                        value="restaurant"
                                        {{ (isset($filters['report_type']) && $filters['report_type'] === 'restaurant') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_restaurant">
                                        Restaurant / Coffee Shop
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="report_type"
                                        id="type_all"
                                        value="all"
                                        {{ (isset($filters['report_type']) && $filters['report_type'] === 'all') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_all">
                                        All Transactions
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="text-end hide-on-print">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa-solid fa-file-invoice-dollar me-1"></i> Generate Report
                            </button>
                            @if($hasSearched && $transactions->isNotEmpty())
                                <button type="button" onclick="window.print()" class="btn btn-success px-4">
                                    <i class="fa-solid fa-print me-1"></i> Print / Save PDF
                                </button>
                            @endif
                            @if($hasSearched)
                                <a href="{{ request()->routeIs('admin.*') ? route('admin.shift-sales') : route('frontdesk.shift-sales') }}" class="btn btn-outline-secondary">
                                    Clear Filters
                                </a>
                            @endif
                            <button
                                type="button"
                                class="btn btn-secondary"
                                onclick="window.history.back()">
                                Close
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </div>

        @if($hasSearched)
            <!-- REPORT OUTPUTS -->
            <div class="col-lg-12">
                <!-- SUMMARIES -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm bg-primary text-white rounded-4">
                            <div class="card-body p-4">
                                <small class="text-white-50">Total Charges Generated</small>
                                <h3 class="fw-bold mt-1 mb-0">₱{{ number_format($totals['charges'], 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm bg-success text-white rounded-4">
                            <div class="card-body p-4">
                                <small class="text-white-50">Total Payments Collected</small>
                                <h3 class="fw-bold mt-1 mb-0">₱{{ number_format($totals['payments'], 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TRANSACTION AUDIT LIST -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">Audit Transactions</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Doc Number</th>
                                        <th>Date / Time</th>
                                        <th>Folio No.</th>
                                        <th>Guest Name</th>
                                        <th>Employee</th>
                                        <th>Charge Code</th>
                                        <th>Payment</th>
                                        <th class="text-end">Charge Amount</th>
                                        <th class="text-end pe-3">Credit Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $tx)
                                        <tr>
                                            <td class="ps-3 fw-semibold small text-primary">{{ $tx->charge_number }}</td>
                                            <td class="small">{{ $tx->timestamp->format('Y-m-d g:i A') }}</td>
                                            <td>{{ $tx->folio?->folio_number ?? '—' }}</td>
                                            <td>
                                                @if($tx->folio && $tx->folio->guest)
                                                    {{ $tx->folio->guest->first_name }} {{ $tx->folio->guest->last_name }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="fw-medium">{{ $tx->user->full_name }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $tx->charge_code }}</span>
                                                <span class="small text-muted">{{ $tx->chargeCode?->description }}</span>
                                            </td>
                                            <td>
                                                @if($tx->payment_method !== 'NONE')
                                                    <span class="badge bg-success-subtle text-success">{{ $tx->payment_method }}</span>
                                                @else
                                                    <span class="text-muted small">None</span>
                                                @endif
                                            </td>
                                            <td class="text-end fw-semibold text-danger">
                                                {{ $tx->charge_amount > 0 ? '₱' . number_format($tx->charge_amount, 2) : '—' }}
                                            </td>
                                            <td class="text-end fw-semibold text-success pe-3">
                                                {{ $tx->credit_amount > 0 ? '₱' . number_format($tx->credit_amount, 2) : '—' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4 text-muted">
                                                No transactions found matching your criteria.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

</div>

@if($hasSearched)
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
                <span>
                    @if(isset($filters['date_from']) && $filters['date_from'])
                        {{ \Carbon\Carbon::parse($filters['date_from'])->format('m/d/Y') }}
                        @if(isset($filters['date_until']) && $filters['date_until'])
                            - {{ \Carbon\Carbon::parse($filters['date_until'])->format('m/d/Y') }}
                        @endif
                    @else
                        {{ now()->format('m/d/Y') }}
                    @endif
                </span>
            </div>
            <div class="d-flex mb-1">
                <span class="fw-bold" style="min-width: 130px; display: inline-block;">EMPLOYEE NAME:</span>
                <span>
                    @if(isset($filters['employee_id']) && $filters['employee_id'])
                        {{ \App\Models\User::find($filters['employee_id'])?->full_name ?? 'All Employees' }}
                    @else
                        {{ auth()->user()->full_name }}
                    @endif
                </span>
            </div>
            <div class="d-flex mb-1">
                <span class="fw-bold" style="min-width: 130px; display: inline-block;">DESIGNATION:</span>
                <span>
                    @if(isset($filters['employee_id']) && $filters['employee_id'])
                        {{ \App\Models\User::find($filters['employee_id'])?->role?->role_name ?? 'Staff' }}
                    @else
                        {{ auth()->user()->role?->role_name ?? 'Staff' }}
                    @endif
                </span>
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
                    @php
                        $shiftDate = isset($filters['date_from']) ? $filters['date_from'] : now()->toDateString();
                        $targetEmpId = (isset($filters['employee_id']) && $filters['employee_id']) ? $filters['employee_id'] : auth()->id();
                        $sched = \App\Models\ShiftSchedule::where('user_id', $targetEmpId)
                            ->whereDate('shift_date', $shiftDate)
                            ->first();
                    @endphp
                    @if($sched)
                        {{ \Carbon\Carbon::parse($sched->scheduled_start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($sched->scheduled_end_time)->format('g:i A') }}
                    @else
                        N/A (No Schedule Found)
                    @endif
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
                    <td colspan="4" class="text-center p-3" style="border: 1px solid #000;">No transactions found.</td>
                </tr>
            @endforelse
            <!-- Total Row -->
            <tr style="border-top: 2px solid #000; font-weight: bold; background-color: #f8f9fa;">
                <td colspan="2" class="p-2 text-end" style="border: 1px solid #000;">Total Balance - ₱</td>
                <td class="p-2 text-end" style="border: 1px solid #000;">{{ number_format($totals['charges'], 2) }}</td>
                <td class="p-2 text-end" style="border: 1px solid #000;">{{ number_format($totals['payments'], 2) }}</td>
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
                <td class="p-1 text-end">₱{{ number_format($totals['charges'] - $totals['payments'], 2) }}</td>
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