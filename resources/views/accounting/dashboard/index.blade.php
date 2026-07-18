@extends('layouts.app')

@section('title', 'Accounting Dashboard')
@section('pageTitle', 'Finance Overview')
@section('pageSubtitle', 'Hotel financial performance at a glance')

@section('content')

<!-- HEADER ROW -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 text-muted">Overview</h5>
    <form method="GET" action="{{ route('accounting.dashboard') }}" class="d-flex align-items-center" data-turbo="true">
        <label for="filter" class="me-2 text-muted mb-0 small">Filter:</label>
        <select name="filter" id="filter" class="form-select form-select-sm" style="width: 220px;" onchange="this.form.requestSubmit()">
            <option value="all" {{ ($filter ?? '') == 'all' ? 'selected' : '' }}>All Time</option>
            <option value="today" {{ ($filter ?? 'today') == 'today' ? 'selected' : '' }}>Today</option>
            <option value="weekly" {{ ($filter ?? '') == 'weekly' ? 'selected' : '' }}>This Week</option>
            <option value="monthly" {{ ($filter ?? '') == 'monthly' ? 'selected' : '' }}>This Month</option>
            <option value="yearly" {{ ($filter ?? '') == 'yearly' ? 'selected' : '' }}>This Year</option>
        </select>
    </form>
</div>

<!-- KPI ROW (DYNAMIC METRICS) -->
<div class="row g-3 mb-4">

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Cash Sales</div>
                <div class="fw-bold fs-4">₱{{ number_format($cashIn, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Credit</div>
                <div class="fw-bold fs-4 text-primary">₱{{ number_format($cashInCard, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Receivables</div>
                <div class="fw-bold fs-4 text-warning">₱{{ number_format($receivables, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-1 shadow-sm">
            <div class="card-body">
                <div class="text-muted large">Expenses</div>
                <div class="fw-bold fs-4 text-danger">₱{{ number_format($expenses, 2) }}</div>
            </div>
        </div>
    </div>

</div>

<!-- CASH SUMMARY -->
<div class="card border-1 shadow-sm mb-4">

    <div class="card-body py-3">

        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="fw-bold">Payment Summary</div>
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
                <div class="fw-bold text-primary">₱{{ number_format($netFlow, 2) }}</div>
            </div>

        </div>

    </div>

</div>

<!-- CHARTS ROW -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card border-1 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold">Revenue Trend (Last 7 Days)</h6>
                <canvas id="revenueTrendChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-1 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Breakdowns</h6>
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
<div class="card border-1 shadow-sm">

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
            // Revenue Trend Line Chart
            revenueTrendChart = new Chart(revenueCanvas, {
                type: 'line',
                data: {
                    labels: data.trend.labels,
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: data.trend.data,
                        borderColor: '#0d6efd',
                        tension: 0.3,
                        fill: true,
                        backgroundColor: 'rgba(13, 110, 253, 0.1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });

            // Payment Method Donut Chart
            paymentMethodChart = new Chart(document.getElementById('paymentMethodChart'), {
                type: 'doughnut',
                data: {
                    labels: data.payment.labels,
                    datasets: [{
                        data: data.payment.data,
                        backgroundColor: ['#198754', '#ffc107', '#0dcaf0'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'right' } }
                }
            });

            // Department Revenue Donut Chart
            departmentChart = new Chart(document.getElementById('departmentChart'), {
                type: 'doughnut',
                data: {
                    labels: data.department.labels,
                    datasets: [{
                        data: data.department.data,
                        backgroundColor: ['#0d6efd', '#6f42c1', '#d63384'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'right' } }
                }
            });
        });
}

document.addEventListener('turbo:load', initCharts);
</script>

@endsection