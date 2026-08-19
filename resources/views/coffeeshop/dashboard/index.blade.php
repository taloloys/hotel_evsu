@extends('layouts.app')

@section('title', 'Coffee Shop Dashboard')
@section('pageTitle', 'Coffee Shop Dashboard')
@section('pageSubtitle', 'Operations overview, alerts, and quick insights')

@section('content')
@include('coffeeshop.partials.alerts')

<style>
:root {
    --coffee-950: #2f1c16;
    --coffee-800: #4e342e;
    --coffee-700: #6d4c41;
    --cream: #f8f5f2;
    --latte: #efe1cf;
    --caramel: #a97142;
    --accent-green: #4caf50;
    --accent-red: #e53935;
    --border-soft: #e7dccf;
    --shadow-soft: 0 14px 34px rgba(78, 52, 46, 0.08);
}

body {
    font-family: 'Inter', 'Segoe UI', sans-serif;
}

.dashboard-shell {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.dashboard-hero {
    background: linear-gradient(135deg, #504538 0%, #3a3025 100%);
    color: white;
    border-radius: 1.25rem;
    padding: 1.5rem 1.5rem 1.4rem;
    box-shadow: var(--shadow-soft);
    overflow: hidden;
    position: relative;
}

.dashboard-hero::after {
    content: '';
    position: absolute;
    inset: auto -30px -60px auto;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: rgba(98, 126, 113, 0.25);
}

.dashboard-hero .hero-title {
    font-size: 1.55rem;
    font-weight: 700;
    font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;
}

.dashboard-hero .hero-subtitle {
    color: rgba(255,255,255,0.88);
    font-size: 1.02rem;
    font-family: 'Lucida Fax', 'Georgia', serif;
}

.dashboard-hero .hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.stat-card {
    border: 1px solid var(--border-soft);
    border-radius: 1rem;
    background: linear-gradient(180deg, #fffdfb 0%, #fcf7f0 100%);
    box-shadow: var(--shadow-soft);
    transition: transform 180ms ease, box-shadow 180ms ease;
    min-height: 138px;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 36px rgba(78, 52, 46, 0.12);
}

.stat-card .icon-wrap {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: white;
    margin-bottom: 0.85rem;
}

.stat-card .stat-label {
    color: #7f6a5a;
    font-size: 0.85rem;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    font-weight: 700;
}

.stat-card .stat-value {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--coffee-800);
}

.stat-card.sales .icon-wrap { background: linear-gradient(135deg, var(--caramel), #c4844f); }
.stat-card.orders .icon-wrap { background: linear-gradient(135deg, var(--coffee-700), #8d5a40); }
.stat-card.tabs .icon-wrap { background: linear-gradient(135deg, #5d8b53, var(--accent-green)); }
.stat-card.low-stock .icon-wrap { background: linear-gradient(135deg, var(--accent-red), #ff6f61); }

.dashboard-panel {
    background: #fffdfb;
    border: 1px solid var(--border-soft);
    border-radius: 1.15rem;
    box-shadow: var(--shadow-soft);
    overflow: hidden;
}

.dashboard-panel .panel-header {
    background: linear-gradient(90deg, #fff8ef 0%, #f8efe3 100%);
    border-bottom: 1px solid var(--border-soft);
    padding: 1rem 1.2rem;
}

#dashboardTabs .nav-link {
    background-color: #f4ebdf;
    color: #6b4d3b;
    border: 1px solid #e6d9c5;
    font-weight: 600;
    font-size: 0.98rem;
    transition: all 180ms ease;
    border-radius: 999px;
    padding: 0.5rem 1.1rem;
}

#dashboardTabs .nav-link:hover {
    background-color: #efe0c5;
    color: var(--coffee-800);
}

#dashboardTabs .nav-link.active {
    background: linear-gradient(135deg, var(--coffee-800), #6b4338);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 8px 20px rgba(78, 52, 46, 0.2);
}

.dashboard-card {
    border: 1px solid var(--border-soft);
    border-radius: 1rem;
    padding: 1.1rem;
    background: linear-gradient(180deg, #fffdfb 0%, #fcf7f0 100%);
    box-shadow: 0 8px 22px rgba(78, 52, 46, 0.06);
    transition: transform 180ms ease, box-shadow 180ms ease;
    height: 100%;
}

.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(78, 52, 46, 0.1);
}

.dashboard-table th {
    background-color: #f6ebdc !important;
    color: #6b4d3b;
    font-size: 0.85rem;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    font-weight: 700;
    padding-top: 0.8rem;
    padding-bottom: 0.8rem;
}

.dashboard-table tbody tr {
    transition: background-color 150ms ease;
}

.dashboard-table tbody tr:hover {
    background-color: #fcf5ea;
}

.dashboard-table td {
    padding-top: 0.95rem;
    padding-bottom: 0.95rem;
    font-size: 1.02rem;
}

.dashboard-list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0e4d6;
}

.dashboard-list-item:last-child {
    border-bottom: 0;
}

.dashboard-pill {
    border-radius: 999px;
    padding: 0.45rem 0.82rem;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.text-brown {
    color: var(--coffee-800) !important;
}

.btn-soft {
    border-radius: 999px;
    padding: 0.7rem 1.25rem;
    font-size: 1.02rem;
    font-weight: 600;
    transition: transform 180ms ease, box-shadow 180ms ease;
}

.btn-soft:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(78, 52, 46, 0.12);
}

@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(229, 57, 53, 0.7);
    }
    70% {
        transform: scale(1.05);
        box-shadow: 0 0 0 10px rgba(229, 57, 53, 0);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(229, 57, 53, 0);
    }
}
.animate-pulse {
    animation: pulse 2s infinite;
}
.uppercase-label {
    letter-spacing: 0.06em;
    text-transform: uppercase;
    font-size: 0.82rem;
}
.product-image-container img {
    border: 3px solid #fff;
    box-shadow: 0 8px 16px rgba(78, 52, 46, 0.12);
}
.hover-opacity:hover {
    opacity: 0.8;
}
.pulse-live {
    display: inline-block;
    width: 8px;
    height: 8px;
    background-color: var(--accent-green);
    border-radius: 50%;
    box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7);
    animation: livePulse 1.8s infinite;
}
@keyframes livePulse {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7);
    }
    70% {
        transform: scale(1);
        box-shadow: 0 0 0 6px rgba(76, 175, 80, 0);
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(76, 175, 80, 0);
    }
}
.hover-translate:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(78, 52, 46, 0.08) !important;
}
</style>

<div class="dashboard-shell">
    @if($inventoryOverview['needs_restocking'] > 0)
        <div class="alert alert-danger d-flex align-items-center justify-content-between border-0 shadow-sm rounded-4 mb-1 p-3" style="background: linear-gradient(135deg, #fff5f5 0%, #ffe3e3 100%);">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center animate-pulse" style="width: 38px; height: 38px; flex-shrink: 0; box-shadow: 0 0 12px rgba(229, 57, 53, 0.4);">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-danger" style="font-size: 0.9rem;">Inventory Warning</h6>
                    <p class="mb-0 text-muted small">{{ $inventoryOverview['needs_restocking'] }} product(s) have reached or fallen below their minimum stock levels.</p>
                </div>
            </div>
            <a href="{{ route('coffeeshop.inventory') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold shadow-sm" style="font-size: 0.8rem;">
                Restock Now
            </a>
        </div>
    @endif

    <div class="row g-3 mb-2">
        <!-- Hero Section -->
        <div class="col-lg-6 col-xl-7">
            <div class="dashboard-hero h-100 d-flex flex-column justify-content-between" style="min-height: 240px;">
                <div>
                    <div class="hero-title mt-1">Freshly brewed insights for the floor</div>
                    <div class="hero-subtitle mt-2">Track sales, monitor stock, and keep service moving without extra clicks.</div>
                </div>
                <div class="hero-actions mt-4">
                    <a href="{{ route('coffeeshop.pos') }}" class="btn btn-soft text-white" style="background: #c2a889; border: none; font-family: 'Lucida Fax', 'Georgia', serif;">
                        <i class="fa-solid fa-cash-register me-2"></i>Open POS
                    </a>
                    <a href="{{ route('coffeeshop.orders') }}" class="btn btn-soft text-white" style="border: 1px solid #c2a889; background: transparent; font-family: 'Lucida Fax', 'Georgia', serif;">
                        <i class="fa-solid fa-receipt me-2"></i>View Orders
                    </a>
                </div>
            </div>
        </div>

        <!-- Animated Slideshow Card -->
        <div class="col-lg-6 col-xl-5">
            <div class="dashboard-panel h-100 border-0 shadow-sm position-relative" style="background: linear-gradient(135deg, #fffdfb 0%, #fcf7f0 100%); border-radius: 1.25rem; min-height: 240px;">
                <div id="statsCarousel" class="carousel slide carousel-fade h-100 d-flex flex-column" data-bs-ride="carousel" data-bs-interval="7000" data-bs-pause="hover">
                    
                    <div class="carousel-inner flex-grow-1 p-4">
                        
                        <!-- Slide 1: Featured Product -->
                        <div class="carousel-item active h-100">
                            <div class="d-flex flex-column h-100">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge fw-bold px-3 py-1.5 rounded-pill uppercase-label" style="background: #627e71; color: #ffffff; font-family: 'Lucida Fax', 'Georgia', serif;">
                                        <i class="fa-solid fa-star me-1" style="color: #c2a889;"></i> Featured Product
                                    </span>
                                    <span class="text-muted small" style="font-size: 0.75rem;">Month of {{ now()->format('F Y') }}</span>
                                </div>
                                
                                @if($featuredProduct)
                                    <div class="d-flex align-items-center gap-3 flex-grow-1 my-auto">
                                        <div class="product-image-container position-relative" style="flex-shrink: 0;">
                                            @if($featuredProduct['image_url'])
                                                <img src="{{ $featuredProduct['image_url'] }}" class="rounded-4 shadow-sm object-fit-cover" style="width: 75px; height: 75px;">
                                            @else
                                                <div class="bg-secondary-subtle rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 75px; height: 75px;">
                                                    <i class="fa-solid fa-mug-hot fa-2x text-secondary"></i>
                                                </div>
                                            @endif
                                            <span class="position-absolute top-0 start-0 translate-middle badge rounded-circle bg-danger p-1" style="font-size: 0.6rem; min-width: 18px;">
                                                ★
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold text-dark mb-1">{{ $featuredProduct['name'] }}</h6>
                                            <span class="badge bg-light text-brown border border-soft px-2 py-0.5 mb-2" style="font-size: 0.7rem;">{{ $featuredProduct['category_name'] }} • {{ $featuredProduct['category_type'] }}</span>
                                            
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <div class="small text-muted" style="font-size: 0.7rem;">Orders</div>
                                                    <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ number_format($featuredProduct['number_of_orders']) }}</div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="small text-muted" style="font-size: 0.7rem;">Sold Qty</div>
                                                    <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ number_format($featuredProduct['total_quantity_sold']) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2 border-top pt-2 d-flex justify-content-between align-items-center">
                                        <span class="small text-muted" style="font-size: 0.75rem;">Total Monthly Revenue</span>
                                        <span class="fw-extrabold text-brown mb-0" style="font-size: 0.95rem; font-weight: 800;">₱{{ number_format($featuredProduct['total_revenue'], 2) }}</span>
                                    </div>
                                @else
                                    <div class="text-center py-4 my-auto text-muted">
                                        <i class="fa-solid fa-box-open fa-2x mb-2 text-secondary"></i>
                                        <p class="mb-0">No sales data for this month yet.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Slide 2: Best-Selling Products -->
                        <div class="carousel-item h-100">
                            <div class="d-flex flex-column h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-success text-white fw-bold px-3 py-1.5 rounded-pill uppercase-label">
                                        <i class="fa-solid fa-ranking-star me-1"></i> Best Sellers
                                    </span>
                                    <span class="text-muted small" style="font-size: 0.75rem;">Month of {{ now()->format('F Y') }}</span>
                                </div>

                                <div class="flex-grow-1 d-flex flex-column justify-content-center">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle mb-0" style="font-size: 0.8rem;">
                                            <thead>
                                                <tr class="text-muted border-bottom" style="font-size: 0.7rem;">
                                                    <th>Item</th>
                                                    <th class="text-center">Qty</th>
                                                    <th class="text-end">Revenue</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($bestSellersData as $index => $item)
                                                    <tr>
                                                        <td class="py-1">
                                                            <div class="d-flex align-items-center gap-1">
                                                                <span class="fw-bold text-muted" style="font-size: 0.75rem; width: 12px;">{{ $index + 1 }}.</span>
                                                                <div>
                                                                    <div class="fw-semibold text-dark" style="font-size: 0.78rem;">{{ $item['name'] }}</div>
                                                                    <div class="text-muted" style="font-size: 0.65rem;">{{ $item['category_name'] }} • {{ $item['category_type'] }}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center fw-bold py-1">{{ $item['total_qty'] }}</td>
                                                        <td class="text-end text-brown fw-semibold py-1">₱{{ number_format($item['total_revenue'], 2) }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted py-3">No data available.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 3: Sales Overview -->
                        <div class="carousel-item h-100">
                            <div class="d-flex flex-column h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary text-white fw-bold px-3 py-1.5 rounded-pill uppercase-label">
                                        <i class="fa-solid fa-chart-line me-1"></i> Sales Overview
                                    </span>
                                    <span class="text-muted small" style="font-size: 0.75rem;">Real-Time</span>
                                </div>

                                <div class="flex-grow-1 d-flex flex-column justify-content-center">
                                    <div class="row g-1 text-center mb-2 border-bottom pb-2">
                                        <div class="col-4 border-end">
                                            <div class="text-muted small" style="font-size: 0.65rem; text-transform: uppercase;">Today (Net)</div>
                                            <div class="fw-bold text-brown" style="font-size: 0.8rem;">₱{{ number_format($salesOverview['today_revenue'], 2) }}</div>
                                        </div>
                                        <div class="col-4 border-end">
                                            <div class="text-muted small" style="font-size: 0.65rem; text-transform: uppercase;">Weekly (Net)</div>
                                            <div class="fw-bold text-brown" style="font-size: 0.8rem;">₱{{ number_format($salesOverview['weekly_revenue'], 2) }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-muted small" style="font-size: 0.65rem; text-transform: uppercase;">Expenses (Today)</div>
                                            <div class="fw-bold text-danger" style="font-size: 0.8rem;">₱{{ number_format($salesOverview['today_expenses'], 2) }}</div>
                                        </div>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="bg-light-subtle p-1.5 rounded border border-soft text-center" style="background-color: #faf6f0;">
                                                <div class="text-muted small" style="font-size: 0.65rem;">Orders Today</div>
                                                <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $salesOverview['today_orders'] }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="bg-light-subtle p-1.5 rounded border border-soft text-center" style="background-color: #faf6f0;">
                                                <div class="text-muted small" style="font-size: 0.65rem;">Pending Orders</div>
                                                <div class="fw-bold text-warning-emphasis" style="font-size: 0.85rem;">{{ $salesOverview['pending_orders'] }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="bg-light-subtle p-1.5 rounded border border-soft text-center" style="background-color: #faf6f0;">
                                                <div class="text-muted small" style="font-size: 0.65rem;">Completed Today</div>
                                                <div class="fw-bold text-success" style="font-size: 0.85rem;">{{ $salesOverview['completed_today'] }} <span class="text-muted small" style="font-size: 0.65rem;">({{ $salesOverview['completed_overall'] }})</span></div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="bg-light-subtle p-1.5 rounded border border-soft text-center" style="background-color: #faf6f0;">
                                                <div class="text-muted small" style="font-size: 0.65rem;">Cancelled Today</div>
                                                <div class="fw-bold text-danger" style="font-size: 0.85rem;">{{ $salesOverview['cancelled_today'] }} <span class="text-muted small" style="font-size: 0.65rem;">({{ $salesOverview['cancelled_overall'] }})</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 4: Inventory Overview -->
                        <div class="carousel-item h-100">
                            <div class="d-flex flex-column h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-danger text-white fw-bold px-3 py-1.5 rounded-pill uppercase-label">
                                        <i class="fa-solid fa-boxes-stacked me-1"></i> Inventory Overview
                                    </span>
                                    <span class="text-muted small" style="font-size: 0.75rem;">Stock Status</span>
                                </div>

                                <div class="flex-grow-1 d-flex flex-column justify-content-center">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center gap-2 p-1.5 bg-light-subtle rounded border border-soft" style="background-color: #faf6f0;">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger-subtle text-danger" style="width: 28px; height: 28px; flex-shrink: 0; font-size: 0.75rem;">
                                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                                </div>
                                                <div>
                                                    <div class="text-muted small" style="font-size: 0.65rem;">Low Stock</div>
                                                    <div class="fw-bold text-dark" style="font-size: 0.8rem;">{{ $inventoryOverview['low_stock'] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center gap-2 p-1.5 bg-light-subtle rounded border border-soft" style="background-color: #faf6f0;">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary-subtle text-secondary" style="width: 28px; height: 28px; flex-shrink: 0; font-size: 0.75rem;">
                                                    <i class="fa-solid fa-circle-xmark"></i>
                                                </div>
                                                <div>
                                                    <div class="text-muted small" style="font-size: 0.65rem;">Out of Stock</div>
                                                    <div class="fw-bold text-dark" style="font-size: 0.8rem;">{{ $inventoryOverview['out_of_stock'] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center gap-2 p-1.5 bg-light-subtle rounded border border-soft" style="background-color: #faf6f0;">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 28px; height: 28px; flex-shrink: 0; font-size: 0.75rem;">
                                                    <i class="fa-solid fa-check"></i>
                                                </div>
                                                <div>
                                                    <div class="text-muted small" style="font-size: 0.65rem;">Available</div>
                                                    <div class="fw-bold text-dark" style="font-size: 0.8rem;">{{ $inventoryOverview['total_available'] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center gap-2 p-1.5 bg-light-subtle rounded border border-soft" style="background-color: #faf6f0;">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning-subtle text-warning" style="width: 28px; height: 28px; flex-shrink: 0; font-size: 0.75rem;">
                                                    <i class="fa-solid fa-truck-ramp-box"></i>
                                                </div>
                                                <div>
                                                    <div class="text-muted small" style="font-size: 0.65rem;">Restock Needed</div>
                                                    <div class="fw-bold text-warning-emphasis" style="font-size: 0.8rem;">{{ $inventoryOverview['needs_restocking'] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Carousel Controls -->
                    <div class="d-flex justify-content-between align-items-center px-4 pb-3 mt-auto border-top pt-2">
                        <div class="carousel-indicators position-static m-0">
                            <button type="button" data-bs-target="#statsCarousel" data-bs-slide-to="0" class="active bg-dark" aria-current="true" aria-label="Featured Product" style="width: 8px; height: 8px; border-radius: 50%; border: none;"></button>
                            <button type="button" data-bs-target="#statsCarousel" data-bs-slide-to="1" class="bg-dark" aria-label="Best Sellers" style="width: 8px; height: 8px; border-radius: 50%; border: none;"></button>
                            <button type="button" data-bs-target="#statsCarousel" data-bs-slide-to="2" class="bg-dark" aria-label="Sales Overview" style="width: 8px; height: 8px; border-radius: 50%; border: none;"></button>
                            <button type="button" data-bs-target="#statsCarousel" data-bs-slide-to="3" class="bg-dark" aria-label="Inventory Overview" style="width: 8px; height: 8px; border-radius: 50%; border: none;"></button>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-dark rounded-circle p-0" type="button" data-bs-target="#statsCarousel" data-bs-slide="prev" style="width: 26px; height: 26px; font-size: 0.75rem; display: flex; align-items: center; justify-content: center;" title="Previous slide">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-dark rounded-circle p-0" type="button" data-bs-target="#statsCarousel" data-bs-slide="next" style="width: 26px; height: 26px; font-size: 0.75rem; display: flex; align-items: center; justify-content: center;" title="Next slide">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <!-- Left Column: Live Order Stream -->
        <div class="col-lg-8">
            <div class="dashboard-panel border-0 shadow-sm" style="background: #fffdfb; border-radius: 1.25rem; overflow: hidden;">
                <div class="panel-header d-flex justify-content-between align-items-center p-3 border-bottom" style="background: #f5ebe0; border-color: #e7dccf !important;">
                    <div>
                        <h5 class="fw-bold mb-1 font-display" style="color: #504538; font-size: 1.2rem;"><i class="fa-solid fa-receipt me-2" style="color: #334c42;"></i>Live Order Feed</h5>
                        <div class="font-body" style="color: #827567; font-size: 0.95rem;">Real-time café transaction stream. Updates dynamically.</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="pulse-live"></span>
                        <span class="fw-bold uppercase-label" style="color: #334c42; font-size: 0.85rem;">Live Monitor</span>
                    </div>
                </div>
                <div class="p-3">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 dashboard-table">
                            <thead>
                                <tr style="font-size: 0.88rem; background-color: #f8f3ed; color: #504538; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif; font-weight: 600; text-transform: uppercase;">
                                    <th class="text-nowrap" style="color: #504538; padding: 0.9rem 0.75rem;">Order</th>
                                    <th style="color: #504538; padding: 0.9rem 0.75rem;">Customer</th>
                                    <th style="color: #504538; padding: 0.9rem 0.75rem;">Ordered Items</th>
                                    <th style="color: #504538; padding: 0.9rem 0.75rem;">Total</th>
                                    <th style="color: #504538; padding: 0.9rem 0.75rem;">Payment Method</th>
                                    <th style="color: #504538; padding: 0.9rem 0.75rem;">Status</th>
                                    <th style="color: #504538; padding: 0.9rem 0.75rem;">Order Time</th>
                                </tr>
                            </thead>
                            <tbody id="recent-orders-rows" style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                @include('coffeeshop.dashboard.partials.recent_orders', ['recentOrders' => $recentOrders])
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Interactive Café Insights -->
        <div class="col-lg-4">
            <!-- Insight 1: Open Tabs Monitor -->
            <div class="dashboard-panel mb-3 border-0 shadow-sm p-3" style="background: linear-gradient(135deg, #fffdfb 0%, #fcf7f0 100%); border-radius: 1.25rem;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 font-display" style="color: #504538; font-size: 0.98rem;"><i class="fa-solid fa-folder-open me-2" style="color: #627e71;"></i>Open Tabs</h6>
                    <span class="badge rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.75rem; background: rgba(98, 126, 113, 0.12); color: #334c42; border: 1px solid #627e71;">{{ $openTabs->count() }} active</span>
                </div>
                <div class="d-flex flex-column gap-2" style="max-height: 200px; overflow-y: auto;">
                    @forelse($openTabs->take(5) as $tab)
                        <div class="d-flex justify-content-between align-items-center p-2.5 p-2 rounded bg-white border border-light shadow-sm hover-translate" style="transition: all 0.2s ease;">
                            <div>
                                <div class="fw-semibold text-dark" style="font-size: 0.92rem; font-family: 'Lucida Fax', 'Georgia', serif;">{{ $tab->tab_name }}</div>
                                <div style="color: #827567; font-size: 0.78rem; font-family: 'Lucida Fax', 'Georgia', serif;">
                                    {{ $tab->tab_type === 'room' ? 'Room ' . ($tab->room?->room_number ?? 'N/A') : 'Walk-in' }}
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold" style="color: #334c42; font-size: 0.95rem; font-family: 'Lucida Fax', 'Georgia', serif;">₱{{ number_format($tab->total, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3 font-body" style="color: #827567; font-size: 0.9rem;">No active tabs open.</div>
                    @endforelse
                </div>
            </div>

            <!-- Insight 2: Category Distribution Chart -->
            <div class="dashboard-panel border-0 shadow-sm p-3.5 p-3 mb-3" style="background: linear-gradient(135deg, #fffdfb 0%, #fcf7f0 100%); border-radius: 1.25rem;">
                <h6 class="fw-bold mb-3 font-display" style="color: #504538; font-size: 0.98rem;"><i class="fa-solid fa-chart-pie me-2" style="color: #334c42;"></i>Category Sales</h6>
                <div class="d-flex flex-column gap-2.5 gap-2">
                    @php
                        $barColors = ['#334c42', '#627e71', '#c2a889', '#827567'];
                    @endphp
                    @foreach($categoryDistribution as $index => $cat)
                        @php
                            $color = $barColors[$index % count($barColors)];
                        @endphp
                        <div>
                            <div class="d-flex justify-content-between text-dark mb-1" style="font-size: 0.88rem; font-family: 'Lucida Fax', 'Georgia', serif;">
                                <span class="fw-semibold">{{ $cat['name'] }}</span>
                                <span style="color: #827567; font-weight: 600;">{{ $cat['percentage'] }}% @if(isset($cat['qty']) && $cat['qty'] > 0)· {{ $cat['qty'] }} sold @endif</span>
                            </div>
                            <div class="progress" style="height: 8px; background-color: #efe2d5; border-radius: 99px; overflow: hidden;">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: {{ $cat['percentage'] }}%; transition: width 1.5s cubic-bezier(0.1, 1, 0.1, 1); background-color: {{ $color }} !important;" 
                                     aria-valuenow="{{ $cat['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Insight 3: Hourly Peak Period -->
            <div class="dashboard-panel border-0 shadow-sm p-3.5 p-4" style="background: linear-gradient(135deg, #fffdfb 0%, #fcf7f0 100%); border-radius: 1.25rem;">
                <h6 class="fw-bold mb-3 font-display" style="color: #504538; font-size: 0.98rem;"><i class="fa-solid fa-clock me-2" style="color: #334c42;"></i>Peak Operations</h6>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger-subtle text-danger" style="width: 48px; height: 48px; flex-shrink: 0;">
                        <i class="fa-solid fa-fire animate-pulse" style="font-size: 1.2rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark mb-1 font-display" style="font-size: 0.95rem;">Peak Period (08:00 AM - 11:00 AM)</div>
                        <div class="font-body" style="color: #827567; font-size: 0.84rem; line-height: 1.4;">Current capacity load: Moderate. Peak demand expected around breakfast & coffee rush.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ordersTbody = document.getElementById('recent-orders-rows');
    if (!ordersTbody) return;

    const url = "{{ route('coffeeshop.dashboard.recent-orders-partial') }}";
    let pollInterval = null;

    function fetchRecentOrders() {
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                ordersTbody.style.transition = 'opacity 0.2s ease';
                ordersTbody.style.opacity = '0.5';
                
                setTimeout(() => {
                    ordersTbody.innerHTML = html;
                    ordersTbody.style.opacity = '1';
                }, 200);
            })
            .catch(error => {
                console.error('Error fetching recent orders:', error);
            });
    }

    function startPolling() {
        if (!pollInterval) {
            pollInterval = setInterval(fetchRecentOrders, 5000);
        }
    }

    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }

    startPolling();

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopPolling();
        } else {
            startPolling();
        }
    });
});
</script>
@endpush
@endsection
