@extends('layouts.app')

@section('title', 'Accounting Dashboard')
@section('pageTitle', 'Finance Overview')
@section('pageSubtitle', 'Hotel financial performance at a glance')

@section('content')

<!-- HEADER ROW -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold" style="color: #504538; font-family: 'Franklin Gothic Medium', sans-serif;">Overview</h5>
    <form method="GET" action="{{ route('accounting.dashboard') }}" id="dashboardFilterForm" data-turbo="true">
        <div class="dropdown">
            <button class="btn bg-white d-flex align-items-center gap-2 px-3 position-relative shadow-sm"
                    type="button"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    style="height: 45px; border-radius: 0.5rem; border: 1px solid #c2a889; color: #504538; font-size: 1rem;">
                <i class="fa-solid fa-filter" style="color: #627e71;"></i>
                <span class="fw-semibold">Filter</span>
                @if(isset($filter) && !in_array($filter, ['today', '']))
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                 onclick="event.stopPropagation()"
                 style="min-width: 240px; border-radius: 0.75rem; z-index: 1055;">
                <label class="form-label small mb-1 fw-semibold text-muted">Time Period</label>
                <select name="filter" id="filter" class="form-select mb-3 shadow-none" style="height:38px; border-radius:0.5rem; border: 1px solid #827567;">
                    <option value="all" {{ ($filter ?? '') == 'all' ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ ($filter ?? 'today') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="weekly" {{ ($filter ?? '') == 'weekly' ? 'selected' : '' }}>This Week</option>
                    <option value="monthly" {{ ($filter ?? '') == 'monthly' ? 'selected' : '' }}>This Month</option>
                    <option value="yearly" {{ ($filter ?? '') == 'yearly' ? 'selected' : '' }}>This Year</option>
                </select>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn text-white w-50 fw-semibold" style="height: 38px; background-color: #334c42; border: none;">Apply</button>
                    <a href="{{ route('accounting.dashboard') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center fw-semibold" style="height: 38px; border: 1px solid #827567; color: #504538;">Reset</a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- KPI ROW (DYNAMIC METRICS) -->
<div class="row g-3 mb-4">

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm rounded-4">
            <div class="card-body">
                <div class="text-muted small mb-1">Cash Sales</div>
                <div class="fw-bold fs-4" style="color: #504538;">₱{{ number_format($cashIn, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm rounded-4">
            <div class="card-body">
                <div class="text-muted small mb-1">Credit</div>
                <div class="fw-bold fs-4" style="color: #504538;">₱{{ number_format($cashInCard, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm rounded-4">
            <div class="card-body">
                <div class="text-muted small mb-1">Receivables</div>
                <div class="fw-bold fs-4" style="color: #827567;">₱{{ number_format($receivables, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm rounded-4">
            <div class="card-body">
                <div class="text-muted small mb-1">Expenses</div>
                <div class="fw-bold fs-4 text-danger">₱{{ number_format($expenses, 2) }}</div>
            </div>
        </div>
    </div>

</div>

<!-- CASH SUMMARY -->
<div class="card border-1 shadow-sm rounded-4 mb-4">

    <div class="card-body py-3">

        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-bold" style="color: #504538;">Payment Summary</div>
            <small class="text-muted">Net collections vs. operational costs</small>
        </div>

        <div class="d-flex justify-content-between">

            <div>
                <div class="text-muted small">Cash In (Cash)</div>
                <div class="fw-bold text-success">₱{{ number_format($cashIn, 2) }}</div>
            </div>

            <div>
                <div class="text-muted small">Cash In (Credit Card)</div>
                <div class="fw-bold text-success">₱{{ number_format($cashInCard, 2) }}</div>
            </div>

            <div>
                <div class="text-muted small">Cash Out (Expenses)</div>
                <div class="fw-bold text-danger">₱{{ number_format($cashOut, 2) }}</div>
            </div>

            <div>
                <div class="text-muted small">Net Flow</div>
                <div class="fw-bold" style="color: #504538;">₱{{ number_format($netFlow, 2) }}</div>
            </div>

        </div>

    </div>

</div>

<!-- CHARTS ROW -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card border-1 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <h6 class="fw-bold" style="color: #504538;">Revenue Trend (Last 7 Days)</h6>
                <canvas id="revenueTrendChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-1 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3" style="color: #504538;">Breakdowns</h6>
                <div style="height: 150px; position: relative;">
                    <canvas id="paymentMethodChart"></canvas>
                </div>
                <hr>
                <div style="height: 150px; position: relative;">
                    <canvas id="departmentChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MAIN TABLE -->
<div class="card border-1 shadow-sm rounded-4 overflow-hidden">

    <div class="card-body">

        <div class="mb-3">
            <div class="fw-bold" style="color: #504538;">Recent Transactions</div>
            <small class="text-muted">Latest financial activity across hotel operations</small>
        </div>

        <div class="table-responsive">

            <table class="table align-middle mb-0">

                <thead style="background-color: #f8f3ed; border-bottom: 1px solid #e5e7eb;">
                    <tr class="text-muted small fw-bold">
                        <th class="ps-3" style="color: #504538;">REF / INVOICE</th>
                        <th style="color: #504538;">TYPE</th>
                        <th style="color: #504538;">DESCRIPTION</th>
                        <th style="color: #504538;">GUEST</th>
                        <th style="color: #504538;">POSTED BY</th>
                        <th style="color: #504538;">STATUS</th>
                        <th class="text-end pe-3" style="color: #504538;">AMOUNT</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($recentTransactions as $tx)
                        <tr style="border-bottom: 1px solid #f0f0f0;">
                            <td class="ps-3 fw-semibold" style="color: #2c241d; font-size: 0.95rem;">{{ $tx->charge_number ?? 'TX-' . $tx->transaction_id }}</td>
                            <td>
                                @if($tx->credit_amount > 0)
                                    <span class="badge rounded-pill px-2.5 py-1 fw-semibold" style="background-color: #198754; color: #ffffff; font-size: 0.78rem;">Payment</span>
                                @else
                                    <span class="badge rounded-pill px-2.5 py-1 fw-semibold" style="background-color: #334c42; color: #ffffff; font-size: 0.78rem;">Charge</span>
                                @endif
                            </td>
                            <td class="fw-medium text-uppercase" style="color: #2c241d; font-size: 0.90rem;">{{ $tx->chargeCode->description ?? $tx->reference_notes }}</td>
                            <td class="fw-medium" style="color: #2c241d; font-size: 0.95rem;">
                                @if($tx->folio && $tx->folio->guest)
                                    {{ $tx->folio->guest->first_name }} {{ $tx->folio->guest->last_name }}
                                @else
                                    <span class="text-muted">Non-guest</span>
                                @endif
                            </td>
                            <td style="color: #554d46; font-size: 0.90rem;">{{ $tx->user?->full_name ?? 'System' }}</td>
                            <td><span class="badge bg-success-subtle text-success fw-semibold">Posted</span></td>
                            <td class="text-end pe-3 fw-bold" style="font-size: 0.98rem; {{ $tx->credit_amount > 0 ? 'color: #198754;' : 'color: #2c241d;' }}">
                                @if($tx->credit_amount > 0)
                                    ₱{{ number_format($tx->credit_amount, 2) }}
                                @else
                                    ₱{{ number_format($tx->charge_amount, 2) }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No recent transactions found.</td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-3">
            {{ $recentTransactions->links() }}
        </div>

    </div>

</div>

<!-- Chart.js and Data Fetching -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let revenueTrendChart = null;
let paymentMethodChart = null;
let departmentChart = null;

function initCharts() {
    const revenueCanvas = document.getElementById('revenueTrendChart');
    if (!revenueCanvas) return; // not on this page

    if (revenueTrendChart) {
        revenueTrendChart.destroy();
    }
    if (paymentMethodChart) {
        paymentMethodChart.destroy();
    }
    if (departmentChart) {
        departmentChart.destroy();
    }

    const filterUrl = new URL('{{ route('accounting.analytics.data') }}', window.location.origin);
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.has('filter')) {
        filterUrl.searchParams.append('filter', urlParams.get('filter'));
    }

    fetch(filterUrl)
        .then(response => response.json())
        .then(data => {
            if (!document.body.contains(revenueCanvas)) return;

            // Revenue Trend Line Chart
            if (revenueCanvas) {
                revenueTrendChart = new Chart(revenueCanvas, {
                    type: 'line',
                    data: {
                        labels: data.trend.labels,
                        datasets: [{
                            label: 'Revenue (₱)',
                            data: data.trend.data,
                            borderColor: '#334c42',
                            tension: 0.3,
                            fill: true,
                            backgroundColor: 'rgba(98, 126, 113, 0.15)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                });
            }

            // Payment Method Donut Chart
            const paymentCanvas = document.getElementById('paymentMethodChart');
            if (paymentCanvas && document.body.contains(paymentCanvas)) {
                paymentMethodChart = new Chart(paymentCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: data.payment.labels,
                        datasets: [{
                            data: data.payment.data,
                            backgroundColor: ['#334c42', '#c2a889', '#827567'],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'right' } }
                    }
                });
            }

            // Department Revenue Donut Chart
            const departmentCanvas = document.getElementById('departmentChart');
            if (departmentCanvas && document.body.contains(departmentCanvas)) {
                departmentChart = new Chart(departmentCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: data.department.labels,
                        datasets: [{
                            data: data.department.data,
                            backgroundColor: ['#504538', '#627e71', '#c2a889'],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'right' } }
                    }
                });
            }
        });
}

document.addEventListener('turbo:load', initCharts);
</script>

@endsection