@extends('layouts.app')

@section('title', 'Statistics')
@section('pageTitle', 'Sales Statistics')
@section('pageSubtitle', 'Analytics, trends, and inventory suggestions')

@section('content')

@include('coffeeshop.partials.alerts')

{{-- ===================== MAIN DASHBOARD WRAPPER ===================== --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">

    <div class="card-body p-4">

        {{-- ===================== STATS ===================== --}}
        <div class="row g-3 mb-4">

            <div class="col-6 col-lg-3">
                <div class="card border-1 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center py-4">
                        <div class="text-muted small">Today's Sales</div>
                        <h3 class="fw-bold text-primary mb-0">
                            ₱{{ number_format($stats['today_sales'], 2) }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card border-1 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center py-4">
                        <div class="text-muted small">Today's Orders</div>
                        <h3 class="fw-bold mb-0">{{ $stats['today_orders'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card border-1 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center py-4">
                        <div class="text-muted small">Open Tabs</div>
                        <h3 class="fw-bold text-info mb-0">{{ $stats['open_tabs'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card border-1 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center py-4">
                        <div class="text-muted small">Low Stock</div>
                        <h3 class="fw-bold text-danger mb-0">{{ $stats['low_stock_count'] }}</h3>
                    </div>
                </div>
            </div>

        </div>
        <div class="row g-3">

        <div class="col-12">

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

                {{-- HEADER --}}
                <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                    <span>Sales Statistics</span>
                    <small class="text-muted">Analytics • Products • Inventory</small>
                </div>

                {{-- NAV PILLS --}}
                <div class="bg-light px-3 pt-3">

                    <ul class="nav nav-pills nav-fill gap-2" id="statisticsTabs" role="tablist">

                        <li class="nav-item">
                            <button class="nav-link active rounded-pill shadow-sm"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-analytics"
                                    type="button">
                                Analytics
                            </button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link rounded-pill shadow-sm"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-products"
                                    type="button">
                                Product Performance
                            </button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link rounded-pill shadow-sm"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-suggestions"
                                    type="button">
                                Recommendations
                            </button>
                        </li>

                    </ul>

                </div>

                {{-- BODY --}}
                <div class="card-body bg-white">

                    <div class="tab-content">

                        {{-- ================= ANALYTICS ================= --}}
                        <div class="tab-pane fade show active" id="tab-analytics">

                            {{-- FILTER --}}
                            <form method="GET" class="mb-4">

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">
                                            From Date
                                        </label>

                                        <input type="date"
                                            name="date_from"
                                            value="{{ $filters['date_from'] }}"
                                            class="form-control auto-submit">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">
                                            To Date
                                        </label>

                                        <input type="date"
                                            name="date_to"
                                            value="{{ $filters['date_to'] }}"
                                            class="form-control auto-submit">
                                    </div>

                                </div>

                            </form>

                            <div class="row g-3">

                                <div class="col-lg-6">

                                    <div class="p-3 bg-light rounded-4 shadow-sm">

                                        <div class="fw-semibold mb-3">
                                            Daily Sales
                                        </div>

                                        <canvas id="dailySalesChart" height="120"></canvas>

                                    </div>

                                </div>

                                <div class="col-lg-6">

                                    <div class="p-3 bg-light rounded-4 shadow-sm">

                                        <div class="fw-semibold mb-3">
                                            Monthly Sales
                                        </div>

                                        <canvas id="monthlySalesChart" height="120"></canvas>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- ================= PRODUCT PERFORMANCE ================= --}}
                        <div class="tab-pane fade" id="tab-products">

                            <div class="row g-3">

                                {{-- MOST SOLD --}}
                                <div class="col-lg-4">

                                    <div class="p-4 bg-light rounded-4 shadow-sm h-100">

                                        <div class="fw-bold text-success fs-5 mb-3">
                                            Most Sold
                                        </div>

                                        @forelse($mostSold as $item)

                                            <div class="d-flex justify-content-between py-2 border-bottom">

                                                <span>{{ $item->product_name }}</span>

                                                <span class="badge bg-success">
                                                    {{ $item->total_qty }}
                                                </span>

                                            </div>

                                        @empty

                                            <div class="text-muted">
                                                No data
                                            </div>

                                        @endforelse

                                    </div>

                                </div>

                                {{-- LEAST SOLD --}}
                                <div class="col-lg-4">

                                    <div class="p-4 bg-light rounded-4 shadow-sm h-100">

                                        <div class="fw-bold text-warning fs-5 mb-3">
                                            Least Sold
                                        </div>

                                        @forelse($leastSold as $item)

                                            <div class="d-flex justify-content-between py-2 border-bottom">

                                                <span>{{ $item->product_name }}</span>

                                                <span class="badge bg-warning text-dark">
                                                    {{ $item->total_qty }}
                                                </span>

                                            </div>

                                        @empty

                                            <div class="text-muted">
                                                No data
                                            </div>

                                        @endforelse

                                    </div>

                                </div>

                                {{-- TOP REVENUE --}}
                                <div class="col-lg-4">

                                    <div class="p-4 bg-light rounded-4 shadow-sm h-100">

                                        <div class="fw-bold text-primary fs-5 mb-3">
                                            Top Revenue
                                        </div>

                                        @forelse($topRevenue as $item)

                                            <div class="d-flex justify-content-between py-2 border-bottom">

                                                <span>{{ $item->product_name }}</span>

                                                <span class="fw-bold text-primary">
                                                    ₱{{ number_format($item->total_revenue,2) }}
                                                </span>

                                            </div>

                                        @empty

                                            <div class="text-muted">
                                                No data
                                            </div>

                                        @endforelse

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- ================= RECOMMENDATIONS ================= --}}
                        <div class="tab-pane fade" id="tab-suggestions">

                            <div class="row g-3">

                                {{-- RESTOCK --}}
                                <div class="col-lg-6">

                                    <div class="p-4 bg-light rounded-4 shadow-sm h-100">

                                        <div class="fw-bold text-danger fs-5 mb-3">
                                            Suggested Restock
                                        </div>

                                        @forelse($suggestedRestock as $product)

                                            <div class="d-flex justify-content-between py-2 border-bottom">

                                                <span>{{ $product->name }}</span>

                                                <span class="text-danger fw-bold">
                                                    {{ $product->stock_quantity }} left
                                                </span>

                                            </div>

                                        @empty

                                            <div class="text-muted">
                                                No restock suggestions
                                            </div>

                                        @endforelse

                                    </div>

                                </div>

                                {{-- PROMOTE --}}
                                <div class="col-lg-6">

                                    <div class="p-4 bg-light rounded-4 shadow-sm h-100">

                                        <div class="fw-bold text-success fs-5 mb-3">
                                            Suggested Promote
                                        </div>

                                        @forelse($suggestedPromote as $row)

                                            <div class="d-flex justify-content-between py-2 border-bottom">

                                                <span>{{ $row['product']->name }}</span>

                                                <span class="text-muted">
                                                    {{ $row['recent_sales'] }} sold /
                                                    {{ $row['stock_quantity'] }} stock
                                                </span>

                                            </div>

                                        @empty

                                            <div class="text-muted">
                                                No promotion suggestions
                                            </div>

                                        @endforelse

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
{{-- ===================== END WRAPPER ===================== --}}

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" onload="initCharts()"></script>

<script>
    
function initCharts() {
    if (typeof Chart === 'undefined') return;

    const dailyCanvas = document.getElementById('dailySalesChart');
    const monthlyCanvas = document.getElementById('monthlySalesChart');

    const dailyData = @json($dailySales);
    const monthlyData = @json($monthlySales);

    new Chart(dailyCanvas, {
        type: 'line',
        data: {
            labels: dailyData.map(r => r.label),
            datasets: [{ data: dailyData.map(r => r.total), borderColor: '#2563eb' }]
        }
    });

    new Chart(monthlyCanvas, {
        type: 'bar',
        data: {
            labels: monthlyData.map(r => r.label),
            datasets: [{ data: monthlyData.map(r => r.total), backgroundColor: '#6f4e37' }]
        }
    });
}

document.addEventListener('turbo:load', initCharts);
document.querySelectorAll('.auto-submit').forEach(input => {
    input.addEventListener('change', () => {
        input.closest('form').submit();
    });
});

</script>
@endpush