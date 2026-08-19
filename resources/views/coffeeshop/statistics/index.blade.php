@extends('layouts.app')

@section('title', 'Statistics')
@section('pageTitle', 'Sales Statistics')
@section('pageSubtitle', 'Analytics, trends, and inventory suggestions')

@section('content')

@include('coffeeshop.partials.alerts')

<div class="coffeeshop-page-shell">
    <div class="coffeeshop-hero">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
            <div>
                <div class="fw-bold fs-4" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">Coffee shop analytics</div>
                <div class="opacity-75 mt-1" style="font-size: 0.95rem;">Read sales trends, product performance, and inventory suggestions in one polished view.</div>
            </div>
        </div>
    </div>

    <div class="coffeeshop-panel overflow-hidden">

    <div class="card-body p-4">

        {{-- ===================== STATS ===================== --}}
        <div class="row g-3 mb-4">

            <div class="col-6 col-lg-3">
                <div class="card border-1 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center py-4">
                        <div class="text-muted small" style="font-family: 'Franklin Gothic Medium', sans-serif;">Today's Sales</div>
                        <h3 class="fw-bold mb-0" style="color: #504538; font-family: 'Franklin Gothic Medium', sans-serif;">
                            ₱{{ number_format($stats['today_sales'], 2) }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card border-1 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center py-4">
                        <div class="text-muted small" style="font-family: 'Franklin Gothic Medium', sans-serif;">Today's Orders</div>
                        <h3 class="fw-bold mb-0" style="color: #504538; font-family: 'Franklin Gothic Medium', sans-serif;">{{ $stats['today_orders'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card border-1 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center py-4">
                        <div class="text-muted small" style="font-family: 'Franklin Gothic Medium', sans-serif;">Open Tabs</div>
                        <h3 class="fw-bold mb-0" style="color: #334c42; font-family: 'Franklin Gothic Medium', sans-serif;">{{ $stats['open_tabs'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card border-1 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center py-4">
                        <div class="text-muted small" style="font-family: 'Franklin Gothic Medium', sans-serif;">Low Stock</div>
                        <h3 class="fw-bold text-danger mb-0" style="font-family: 'Franklin Gothic Medium', sans-serif;">{{ $stats['low_stock_count'] }}</h3>
                    </div>
                </div>
            </div>

        </div>
        <div class="row g-3">

        <div class="col-12">

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

                {{-- HEADER --}}
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center p-3" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif; color: #504538; font-size: 1.1rem;">
                    <span>Sales Statistics</span>
                    <small class="text-muted fw-normal" style="font-size: 0.85rem;">Analytics • Products • Inventory</small>
                </div>

                {{-- NAV PILLS --}}
                <div class="bg-light px-3 pt-3">

                    <ul class="nav nav-pills nav-fill gap-2 coffeeshop-nav-pills" id="statisticsTabs" role="tablist" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">

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
                                        <label class="form-label small text-muted fw-semibold" style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                            From Date
                                        </label>

                                        <input type="date"
                                            name="date_from"
                                            value="{{ $filters['date_from'] }}"
                                            class="form-control auto-submit shadow-none"
                                            style="border: 1px solid #827567; border-radius: 0.5rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small text-muted fw-semibold" style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                            To Date
                                        </label>

                                        <input type="date"
                                            name="date_to"
                                            value="{{ $filters['date_to'] }}"
                                            class="form-control auto-submit shadow-none"
                                            style="border: 1px solid #827567; border-radius: 0.5rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                    </div>

                                </div>

                            </form>

                            <div class="row g-3">

                                <div class="col-lg-6">

                                    <div class="p-3 bg-light rounded-4 shadow-sm">

                                        <div class="fw-bold mb-3" style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif; color: #2c241d; font-size: 1.05rem;">
                                            Daily Sales
                                        </div>

                                        <canvas id="dailySalesChart" height="120"></canvas>

                                    </div>

                                </div>

                                <div class="col-lg-6">

                                    <div class="p-3 bg-light rounded-4 shadow-sm">

                                        <div class="fw-bold mb-3" style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif; color: #2c241d; font-size: 1.05rem;">
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

                                        <div class="fw-bold fs-5 mb-3" style="color: #627e71; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                            Most Sold
                                        </div>

                                        @forelse($mostSold as $item)

                                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">

                                                <span style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif; color: #2c241d; font-weight: 600;">{{ $item->product_name }}</span>

                                                <span class="badge rounded-pill px-2.5 py-1 fw-semibold" style="background-color: #627e71; color: #ffffff; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                    {{ $item->total_qty }}
                                                </span>

                                            </div>

                                        @empty

                                            <div style="color: #554d46; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                No data
                                            </div>

                                        @endforelse

                                    </div>

                                </div>

                                {{-- LEAST SOLD --}}
                                <div class="col-lg-4">

                                    <div class="p-4 bg-light rounded-4 shadow-sm h-100">

                                        <div class="fw-bold fs-5 mb-3" style="color: #827567; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                            Least Sold
                                        </div>

                                        @forelse($leastSold as $item)

                                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">

                                                <span style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif; color: #2c241d; font-weight: 600;">{{ $item->product_name }}</span>

                                                <span class="badge rounded-pill px-2.5 py-1 fw-semibold" style="background-color: #827567; color: #ffffff; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                    {{ $item->total_qty }}
                                                </span>

                                            </div>

                                        @empty

                                            <div style="color: #554d46; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                No data
                                            </div>

                                        @endforelse

                                    </div>

                                </div>

                                {{-- TOP REVENUE --}}
                                <div class="col-lg-4">

                                    <div class="p-4 bg-light rounded-4 shadow-sm h-100">

                                        <div class="fw-bold fs-5 mb-3" style="color: #2c241d; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                            Top Revenue
                                        </div>

                                        @forelse($topRevenue as $item)

                                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">

                                                <span style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif; color: #2c241d; font-weight: 600;">{{ $item->product_name }}</span>

                                                <span class="fw-bold" style="color: #2c241d; font-size: 1.05rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                    ₱{{ number_format($item->total_revenue, 2) }}
                                                </span>

                                            </div>

                                        @empty

                                            <div style="color: #554d46; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
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

                                        <div class="fw-bold text-danger fs-5 mb-3" style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                            Suggested Restock
                                        </div>

                                        @forelse($suggestedRestock as $product)

                                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">

                                                <span style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif; color: #2c241d; font-weight: 600;">{{ $product->name }}</span>

                                                <span class="text-danger fw-bold" style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                    {{ $product->stock_quantity }} left
                                                </span>

                                            </div>

                                        @empty

                                            <div style="color: #554d46; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                No restock suggestions
                                            </div>

                                        @endforelse

                                    </div>

                                </div>

                                {{-- PROMOTE --}}
                                <div class="col-lg-6">

                                    <div class="p-4 bg-light rounded-4 shadow-sm h-100">

                                        <div class="fw-bold fs-5 mb-3" style="color: #627e71; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                            Suggested Promote
                                        </div>

                                        @forelse($suggestedPromote as $row)

                                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">

                                                <span style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif; color: #2c241d; font-weight: 600;">{{ $row['product']->name }}</span>

                                                <span style="color: #554d46; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                    {{ $row['recent_sales'] }} sold /
                                                    {{ $row['stock_quantity'] !== null ? $row['stock_quantity'].' stock' : '—' }}
                                                </span>

                                            </div>

                                        @empty

                                            <div style="color: #554d46; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
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
            datasets: [{
                label: 'Daily Sales (₱)',
                data: dailyData.map(r => r.total),
                borderColor: '#334c42',
                backgroundColor: 'rgba(98, 126, 113, 0.15)',
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#334c42'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: '#2c241d',
                        font: { family: "'Plus Jakarta Sans', 'Inter', sans-serif" }
                    }
                }
            },
            scales: {
                x: {
                    ticks: { color: '#554d46', font: { family: "'Plus Jakarta Sans', 'Inter', sans-serif" } },
                    grid: { color: '#f0f0f0' }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#554d46',
                        font: { family: "'Plus Jakarta Sans', 'Inter', sans-serif" },
                        callback: function(value) {
                            return '₱' + Number(value).toLocaleString();
                        }
                    },
                    grid: { color: '#f0f0f0' }
                }
            }
        }
    });

    new Chart(monthlyCanvas, {
        type: 'bar',
        data: {
            labels: monthlyData.map(r => r.label),
            datasets: [{
                label: 'Monthly Revenue (₱)',
                data: monthlyData.map(r => r.total),
                backgroundColor: '#504538',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: '#2c241d',
                        font: { family: "'Plus Jakarta Sans', 'Inter', sans-serif" }
                    }
                }
            },
            scales: {
                x: {
                    ticks: { color: '#554d46', font: { family: "'Plus Jakarta Sans', 'Inter', sans-serif" } },
                    grid: { color: '#f0f0f0' }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#554d46',
                        font: { family: "'Plus Jakarta Sans', 'Inter', sans-serif" },
                        callback: function(value) {
                            return '₱' + Number(value).toLocaleString();
                        }
                    },
                    grid: { color: '#f0f0f0' }
                }
            }
        }
    });
}

document.addEventListener('turbo:load', initCharts);
document.querySelectorAll('.auto-submit').forEach(input => {
    input.addEventListener('change', () => {
        const form = input.closest('form');
        if (form.requestSubmit) {
            form.requestSubmit();
        } else {
            form.submit();
        }
    });
});

</script>
@endpush