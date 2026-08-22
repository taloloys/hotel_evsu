@extends('layouts.app')

@section('title', 'Customers')
@section('pageTitle', 'Customer History')
@section('pageSubtitle', 'Order history, frequent customers, open and closed tabs')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="coffeeshop-page-shell">
    <div class="coffeeshop-hero">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
            <div>
                <div class="fw-bold fs-4" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">Customer stories and repeat visits</div>
                <div class="opacity-75 mt-1" style="font-size: 0.95rem;">Review the people behind the orders and keep their history close at hand.</div>
            </div>
        </div>
    </div>

    {{-- ===================== FILTER (RIGHT ALIGNED) ===================== --}}
    <div class="d-flex justify-content-end mb-3">

        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap" id="customersFilterForm">

            <!-- SEARCH -->
            <div style="width: 340px;">
                <div class="input-group" style="border: 1px solid #827567; border-radius: 0.5rem; overflow: hidden; height: 45px;">
                    <span class="input-group-text bg-white border-0 px-3">
                        <i class="fa-solid fa-magnifying-glass" style="color: #627e71;"></i>
                    </span>
                    <input type="text"
                        name="search"
                        id="customerSearch"
                        value="{{ request('search') }}"
                        class="form-control border-0 shadow-none py-2"
                        placeholder="Search customer or order..."
                        autocomplete="off"
                        style="font-size: 1rem; font-family: 'Lucida Fax', 'Georgia', serif;">
                </div>
            </div>

            <!-- FILTER DROPDOWN -->
            <div class="dropdown">
                <button class="btn bg-white d-flex align-items-center gap-2 px-3 position-relative shadow-sm"
                        type="button"
                        data-bs-toggle="dropdown"
                        style="height: 45px; border-radius: 0.5rem; border: 1px solid #c2a889; color: #504538; font-size: 1rem; font-family: 'Lucida Fax', serif;">
                    <i class="fa-solid fa-filter" style="color: #627e71;"></i>
                    <span class="fw-semibold">Filter</span>
                    @if(request('status'))
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                     onclick="event.stopPropagation()"
                     style="min-width: 280px; border-radius: 0.75rem; z-index: 1055; font-family: 'Lucida Fax', 'Georgia', serif;">

                    <!-- Status Filter -->
                    <label class="form-label small mb-1 fw-semibold text-muted" style="font-family: 'Franklin Gothic Medium', sans-serif;">Status</label>
                    <select name="status" class="form-select mb-3 shadow-none" style="height:38px; border-radius:0.5rem; border: 1px solid #827567;">
                        <option value="">All Records</option>
                        <option value="open" @selected($status === 'open')>Open Tabs</option>
                        <option value="closed" @selected($status === 'closed')>Closed Orders</option>
                        <option value="cancelled" @selected($status === 'cancelled')>Cancelled</option>
                        <option value="refunded" @selected($status === 'refunded')>Refunded</option>
                    </select>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn text-white w-50 fw-semibold" style="height: 38px; background-color: #334c42; border: none;">Apply</button>
                        <a href="{{ route('coffeeshop.customers') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center fw-semibold" style="height: 38px; border: 1px solid #827567; color: #504538;">Reset</a>
                    </div>
                </div>
            </div>

        </form>

    </div>

    <div class="coffeeshop-panel">

    <div class="card-body p-3">

        {{-- ===================== CLEAN STATS HEADER ===================== --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6"><div class="coffeeshop-card p-4 text-center h-100"><div class="text-muted small" style="font-family: 'Franklin Gothic Medium', sans-serif;">Total Customers</div><div class="fw-bold fs-4" style="color: #504538; font-family: 'Franklin Gothic Medium', sans-serif;">{{ $orders->pluck('customer_name')->unique()->count() }}</div></div></div>
            <div class="col-md-3 col-6"><div class="coffeeshop-card p-4 text-center h-100"><div class="text-muted small" style="font-family: 'Franklin Gothic Medium', sans-serif;">Active Tabs</div><div class="fw-bold fs-4" style="color: #334c42; font-family: 'Franklin Gothic Medium', sans-serif;">{{ $tabs->where('status', 'open')->count() }}</div></div></div>
            <div class="col-md-3 col-6"><div class="coffeeshop-card p-4 text-center h-100"><div class="text-muted small" style="font-family: 'Franklin Gothic Medium', sans-serif;">Completed</div><div class="fw-bold fs-4 text-success" style="font-family: 'Franklin Gothic Medium', sans-serif;">{{ $orders->where('status', 'closed')->count() }}</div></div></div>
            <div class="col-md-3 col-6"><div class="coffeeshop-card p-4 text-center h-100"><div class="text-muted small" style="font-family: 'Franklin Gothic Medium', sans-serif;">Refunded</div><div class="fw-bold fs-4 text-danger" style="font-family: 'Franklin Gothic Medium', sans-serif;">{{ $orders->where('status', 'refunded')->count() }}</div></div></div>
        </div>

        {{-- ===================== CONTENT (ORDER-STYLE TABS) ===================== --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            <div class="card-body">

                {{-- TAB NAVIGATION --}}
                @php
                    $activeTab = request()->has('tabs_page') ? 'tabs' : 'customers';
                @endphp
                <div class="px-3 pt-3 bg-white border-bottom">

                    <ul class="nav nav-pills nav-fill gap-2 fw-semibold coffeeshop-nav-pills" id="customerNav" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">

                        <li class="nav-item">
                            <button class="nav-link {{ $activeTab === 'customers' ? 'active' : '' }} rounded-pill"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-customers"
                                    type="button">
                                Frequent Customers
                            </button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link {{ $activeTab === 'orders' ? 'active' : '' }} rounded-pill"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-orders"
                                    type="button">
                                Recent Orders
                            </button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link {{ $activeTab === 'tabs' ? 'active' : '' }} rounded-pill"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-tabs"
                                    type="button">
                                Tabs
                            </button>
                        </li>

                    </ul>

                </div>

                {{-- TAB CONTENT --}}
                <div class="tab-content">

                    {{-- ===================== FREQUENT CUSTOMERS ===================== --}}
                    <div class="tab-pane fade {{ $activeTab === 'customers' ? 'show active' : '' }}" id="tab-customers">

                        <div class="table-responsive border rounded-4 bg-white overflow-hidden shadow-sm mt-3">

                            <table class="table align-middle mb-0 coffeeshop-table">

                                <thead style="background-color: #f8f3ed; border-bottom: 1px solid #e5e7eb; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                    <tr>
                                        <th class="ps-4" style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">CUSTOMER</th>
                                        <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">ORDERS</th>
                                        <th class="pe-4 text-end" style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">TOTAL SPENT</th>
                                    </tr>
                                </thead>

                                <tbody style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">

                                    @forelse($frequentCustomers as $customer)

                                        <tr style="border-bottom: 1px solid #f0f0f0;">
                                            <td class="ps-4" style="padding: 1.05rem 1rem; color: #2c241d; font-weight: 600; font-size: 1.05rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                @if(Str::contains(strtolower($customer->customer_name), 'room'))
                                                    <span class="badge rounded-pill me-1 fw-semibold" style="background-color: #627e71; color: #ffffff; font-size: 0.78rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;" title="Hotel Guest Charge"><i class="fa-solid fa-hotel me-1"></i>Guest</span>
                                                @endif
                                                {{ $customer->customer_name }}
                                            </td>

                                            <td style="padding: 1.05rem 1rem; color: #554d46; font-size: 0.98rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                {{ $customer->order_count }} order(s)
                                            </td>

                                            <td class="pe-4 text-end" style="padding: 1.05rem 1rem; color: #2c241d; font-weight: 600; font-size: 1.08rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                ₱{{ number_format($customer->total_spent, 2) }}
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-5">
                                                No customer data yet.
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                    {{-- ===================== ORDERS ===================== --}}
                    <div class="tab-pane fade {{ $activeTab === 'orders' ? 'show active' : '' }}" id="tab-orders">

                        <div class="table-responsive border rounded-4 bg-white overflow-hidden shadow-sm mt-3">

                            <table class="table align-middle mb-0 coffeeshop-table">

                                <thead style="background-color: #f8f3ed; border-bottom: 1px solid #e5e7eb; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                    <tr>
                                        <th class="ps-4" style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">ORDER</th>
                                        <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">CUSTOMER</th>
                                        <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">TOTAL</th>
                                        <th class="pe-4 text-end" style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">STATUS</th>
                                    </tr>
                                </thead>

                                <tbody style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">

                                    @forelse($orders as $order)

                                        <tr style="border-bottom: 1px solid #f0f0f0;">

                                            <td class="ps-4" style="padding: 1.05rem 1rem;">
                                                <a href="{{ route('coffeeshop.orders.show', $order) }}"
                                                class="fw-semibold text-decoration-none hover-opacity" style="color: #627e71; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif; font-size: 1.02rem;">
                                                    {{ $order->order_number }}
                                                </a>
                                            </td>

                                            <td style="padding: 1.05rem 1rem; color: #2c241d; font-weight: 600; font-size: 1.05rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                {{ $order->customer_name }}
                                            </td>

                                            <td style="padding: 1.05rem 1rem; color: #2c241d; font-weight: 600; font-size: 1.08rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                ₱{{ number_format($order->total, 2) }}
                                            </td>

                                            <td class="pe-4 text-end" style="padding: 1.05rem 1rem;">
                                                @php
                                                    $status = strtoupper($order->status);

                                                    $badgeClass = match(strtolower($order->status)) {
                                                        'closed', 'paid', 'completed' => 'badge-status-closed',
                                                        'cancelled' => 'bg-danger text-white',
                                                        'refunded' => 'bg-info text-white',
                                                        default => 'badge-status-open'
                                                    };
                                                @endphp

                                                <span class="coffeeshop-pill fw-semibold {{ $badgeClass }}" style="font-size: 0.88rem; padding: 0.28rem 0.8rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                    {{ $status }}
                                                </span>
                                            </td>

                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-5">
                                                <i class="fa-solid fa-file-circle-xmark d-block mb-2"></i>
                                                No orders found.
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                    {{-- ===================== TABS ===================== --}}
                    <div class="tab-pane fade {{ $activeTab === 'tabs' ? 'show active' : '' }}" id="tab-tabs">

                        <div class="d-flex justify-content-center px-3 pb-4">

                            <div class="w-100" style="max-width: 1100px;">

                                <div class="table-responsive border rounded-4 bg-white overflow-hidden shadow-sm mt-3">

                                    <table class="table align-middle mb-0 coffeeshop-table">

                                        <thead style="background-color: #f8f3ed; border-bottom: 1px solid #e5e7eb; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                            <tr>
                                                <th class="ps-4" style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">TAB</th>
                                                <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">TOTAL</th>
                                                <th class="pe-4 text-end" style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">STATUS</th>
                                            </tr>
                                        </thead>

                                        <tbody style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">

                                            @forelse($tabs as $tab)

                                                <tr style="border-bottom: 1px solid #f0f0f0;">

                                                    <td class="ps-4" style="padding: 1.05rem 1rem; color: #2c241d; font-weight: 600; font-size: 1.05rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                        {{ $tab->tab_name }}
                                                    </td>

                                                    <td style="padding: 1.05rem 1rem; color: #2c241d; font-weight: 600; font-size: 1.08rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                        ₱{{ number_format($tab->total, 2) }}
                                                    </td>

                                                    <td class="pe-4 text-end" style="padding: 1.05rem 1rem;">
                                                        @php
                                                            $badgeClass = match(strtolower($tab->status)) {
                                                                'closed' => 'badge-status-closed',
                                                                'cancelled' => 'bg-danger text-white',
                                                                default => 'badge-status-open'
                                                            };
                                                        @endphp

                                                        <span class="coffeeshop-pill fw-semibold {{ $badgeClass }}" style="font-size: 0.88rem; padding: 0.28rem 0.8rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                            {{ strtoupper($tab->status) }}
                                                        </span>
                                                    </td>

                                                </tr>

                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-5">
                                                        No tabs found.
                                                    </td>
                                                </tr>
                                            @endforelse

                                        </tbody>

                                    </table>

                                </div>

                                @if($tabs->hasPages())
                                    <div class="pt-3 d-flex justify-content-center">
                                        {{ $tabs->links() }}
                                    </div>
                                @endif
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const searchInput = document.getElementById('customerSearch');
        if (searchInput) {
            function debounce(func, wait) {
                let timeout;
                return function (...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            const form = searchInput.closest('form');
            if (form) {
                searchInput.addEventListener('input', debounce(function () {
                    if (form.requestSubmit) {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                }, 500));
            }
        }
    })();

    // Close dropdown on form submit (handles Turbo dynamic page load preservation)
    (function () {
        const form = document.getElementById('customersFilterForm');
        if (form) {
            form.addEventListener('submit', function () {
                const dropdownEl = form.querySelector('[data-bs-toggle="dropdown"]');
                if (dropdownEl) {
                    try {
                        const dropdown = bootstrap.Dropdown.getOrCreateInstance(dropdownEl);
                        if (dropdown) {
                            dropdown.hide();
                        }
                    } catch (e) {}
                    dropdownEl.classList.remove('show');
                    dropdownEl.setAttribute('aria-expanded', 'false');
                    const menu = dropdownEl.nextElementSibling;
                    if (menu) {
                        menu.classList.remove('show');
                    }
                }
            });
        }
    })();
</script>
@endpush
