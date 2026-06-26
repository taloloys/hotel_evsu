@extends('layouts.app')

@section('title', 'Statistics')
@section('pageTitle', 'Sales Statistics')
@section('pageSubtitle', 'Analytics, trends, and inventory suggestions')

@section('content')
@include('coffeeshop.partials.alerts')

<form method="GET" class="row g-2 mb-4">
    <div class="col-auto"><input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="form-control"></div>
    <div class="col-auto"><input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="form-control"></div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Apply</button></div>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Today's Sales</div><h4>₱{{ number_format($stats['today_sales'], 2) }}</h4></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Today's Orders</div><h4>{{ $stats['today_orders'] }}</h4></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Open Tabs</div><h4>{{ $stats['open_tabs'] }}</h4></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Low Stock</div><h4 class="text-danger">{{ $stats['low_stock_count'] }}</h4></div></div></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Daily Sales (14 days)</div>
            <div class="card-body"><canvas id="dailySalesChart" height="120"></canvas></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Monthly Sales</div>
            <div class="card-body"><canvas id="monthlySalesChart" height="120"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Most Sold Products</div>
            <ul class="list-group list-group-flush">
                @forelse($mostSold as $item)
                <li class="list-group-item d-flex justify-content-between"><span>{{ $item->product_name }}</span><span>{{ $item->total_qty }}</span></li>
                @empty
                <li class="list-group-item text-muted">No sales data.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Least Sold Products</div>
            <ul class="list-group list-group-flush">
                @forelse($leastSold as $item)
                <li class="list-group-item d-flex justify-content-between"><span>{{ $item->product_name }}</span><span>{{ $item->total_qty }}</span></li>
                @empty
                <li class="list-group-item text-muted">No sales data.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Top Revenue Items</div>
            <ul class="list-group list-group-flush">
                @forelse($topRevenue as $item)
                <li class="list-group-item d-flex justify-content-between"><span>{{ $item->product_name }}</span><span>₱{{ number_format($item->total_revenue, 2) }}</span></li>
                @empty
                <li class="list-group-item text-muted">No sales data.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Suggested Restock</div>
            <ul class="list-group list-group-flush">
                @forelse($suggestedRestock as $product)
                <li class="list-group-item d-flex justify-content-between"><span>{{ $product->name }}</span><span class="text-danger">{{ $product->stock_quantity }} left</span></li>
                @empty
                <li class="list-group-item text-muted">No restock suggestions.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Suggested Promote</div>
            <ul class="list-group list-group-flush">
                @forelse($suggestedPromote as $row)
                <li class="list-group-item d-flex justify-content-between">
                    <span>{{ $row['product']->name }}</span>
                    <small class="text-muted">{{ $row['recent_sales'] }} sold / {{ $row['stock_quantity'] }} stock</small>
                </li>
                @empty
                <li class="list-group-item text-muted">No promotion suggestions.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" onload="initCharts()"></script>
<script>
function initCharts() {
    if (typeof Chart === 'undefined') return;
    
    const dailyCanvas = document.getElementById('dailySalesChart');
    const monthlyCanvas = document.getElementById('monthlySalesChart');
    
    if (!dailyCanvas || !monthlyCanvas) return;
    
    const existingDaily = Chart.getChart(dailyCanvas);
    if (existingDaily) {
        existingDaily.destroy();
    }
    const existingMonthly = Chart.getChart(monthlyCanvas);
    if (existingMonthly) {
        existingMonthly.destroy();
    }
    
    const dailyData = @json($dailySales);
    const monthlyData = @json($monthlySales);

    new Chart(dailyCanvas, {
        type: 'line',
        data: {
            labels: dailyData.map(row => row.label),
            datasets: [{ label: 'Sales', data: dailyData.map(row => row.total), borderColor: '#2563eb', tension: 0.3 }]
        }
    });

    new Chart(monthlyCanvas, {
        type: 'bar',
        data: {
            labels: monthlyData.map(row => row.label),
            datasets: [{ label: 'Sales', data: monthlyData.map(row => row.total), backgroundColor: '#6f4e37' }]
        }
    });
}

// Call on turbo:load for sub-sequent page navigations
document.addEventListener('turbo:load', initCharts);
</script>
@endpush
