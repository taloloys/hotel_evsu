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
                <div class="fw-bold fs-5">Customer stories and repeat visits</div>
                <div class="opacity-75 mt-1">Review the people behind the orders and keep their history close at hand.</div>
            </div>
        </div>
    </div>

    {{-- ===================== FILTER (RIGHT ALIGNED) ===================== --}}
    <div class="d-flex justify-content-end">

        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap" id="customersFilterForm">

            <!-- SEARCH -->
            <div style="width: 320px;">
                <div class="input-group" style="border: 1px solid black; border-radius: 6px; height: 45px;">
                    <span class="input-group-text bg-white border-0 px-3">
                        <i class="fa-solid fa-magnifying-glass text-muted fs-5"></i>
                    </span>
                    <input type="text"
                        name="search"
                        id="customerSearch"
                        value="{{ request('search') }}"
                        class="form-control border-0 shadow-none py-2"
                        placeholder="Search customer or order..."
                        autocomplete="off"
                        style="font-size: 1.05rem;">
                </div>
            </div>

            <!-- FILTER DROPDOWN -->
            <div class="dropdown">
                <button class="btn btn-outline-dark d-flex align-items-center gap-2 px-3 position-relative"
                        type="button"
                        data-bs-toggle="dropdown"
                        style="height: 45px; border-radius: 6px; border: 1px solid black; font-size: 1.05rem;">
                    <i class="fa-solid fa-filter fs-5"></i>
                    <span>Filter</span>
                    @if(request('status'))
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm"
                     onclick="event.stopPropagation()"
                     style="min-width: 280px; border-radius: 8px; z-index: 1055;">

                    <!-- Status Filter -->
                    <label class="form-label small mb-1 fw-semibold text-muted">Status</label>
                    <select name="status" class="form-select mb-3 shadow-none" style="height:38px; border-radius:4px; border: 1px solid black;">
                        <option value="">All Records</option>
                        <option value="open" @selected($status === 'open')>Open Tabs</option>
                        <option value="closed" @selected($status === 'closed')>Closed Orders</option>
                        <option value="cancelled" @selected($status === 'cancelled')>Cancelled</option>
                        <option value="refunded" @selected($status === 'refunded')>Refunded</option>
                    </select>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-50" style="height: 38px;">Apply</button>
                        <a href="{{ route('coffeeshop.customers') }}" class="btn btn-light w-50 d-flex align-items-center justify-content-center" style="height: 38px;">Reset</a>
                    </div>
                </div>
            </div>

        </form>

    </div>

    <div class="coffeeshop-panel">

    <div class="card-body p-3">

        {{-- ===================== CLEAN STATS HEADER ===================== --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6"><div class="coffeeshop-card p-4 text-center h-100"><div class="text-muted small">Total Customers</div><div class="fw-bold fs-5 text-brown">{{ $orders->pluck('customer_name')->unique()->count() }}</div></div></div>
            <div class="col-md-3 col-6"><div class="coffeeshop-card p-4 text-center h-100"><div class="text-muted small">Active Tabs</div><div class="fw-bold fs-5 text-info">{{ $tabs->where('status', 'open')->count() }}</div></div></div>
            <div class="col-md-3 col-6"><div class="coffeeshop-card p-4 text-center h-100"><div class="text-muted small">Completed</div><div class="fw-bold fs-5 text-success">{{ $orders->where('status', 'closed')->count() }}</div></div></div>
            <div class="col-md-3 col-6"><div class="coffeeshop-card p-4 text-center h-100"><div class="text-muted small">Refunded</div><div class="fw-bold fs-5 text-danger">{{ $orders->where('status', 'refunded')->count() }}</div></div></div>
        </div>

        {{-- ===================== CONTENT (ORDER-STYLE TABS) ===================== --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            <div class="card-body">

                {{-- TAB NAVIGATION --}}
                <div class="px-3 pt-3 bg-white border-bottom">

                    <ul class="nav nav-pills nav-fill gap-2 fw-semibold coffeeshop-nav-pills" id="customerNav">

                        <li class="nav-item">
                            <button class="nav-link active rounded-pill"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-customers"
                                    type="button">
                                Frequent Customers
                            </button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link rounded-pill"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-orders"
                                    type="button">
                                Recent Orders
                            </button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link rounded-pill"
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
                    <div class="tab-pane fade show active" id="tab-customers">

                        <div class="table-responsive border rounded-4 bg-white overflow-hidden shadow-sm">

                            <table class="table align-middle mb-0">

                                <thead class="table-light">
                                    <tr class="text-muted small">
                                        <th class="ps-3">Customer</th>
                                        <th>Orders</th>
                                        <th class="pe-3 text-end">Total Spent</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($frequentCustomers as $customer)

                                        <tr>
                                            <td class="ps-3 fw-semibold">
                                                @if(Str::contains(strtolower($customer->customer_name), 'room'))
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1" title="Hotel Guest Charge"><i class="fa-solid fa-hotel me-1"></i>Guest</span>
                                                @endif
                                                {{ $customer->customer_name }}
                                            </td>

                                            <td class="fw-semibold">
                                                {{ $customer->order_count }}
                                            </td>

                                            <td class="pe-3 text-end fw-bold">
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
                    <div class="tab-pane fade" id="tab-orders">

                        <div class="table-responsive border rounded-4 bg-white overflow-hidden shadow-sm">

                            <table class="table align-middle mb-0">

                                <thead class="table-light">
                                    <tr class="text-muted small">
                                        <th class="ps-3">Order</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th class="pe-3 text-end">Status</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($orders as $order)

                                        <tr>

                                            <td class="ps-3">
                                                <a href="{{ route('coffeeshop.orders.show', $order) }}"
                                                class="fw-semibold text-decoration-none">
                                                    {{ $order->order_number }}
                                                </a>
                                            </td>

                                            <td class="fw-semibold">
                                                {{ $order->customer_name }}
                                            </td>

                                            <td class="fw-bold text-primary">
                                                ₱{{ number_format($order->total, 2) }}
                                            </td>

                                            <td class="pe-3 text-end">
                                                @php
                                                    $status = strtoupper($order->status);

                                                    $badge = match($order->status) {
                                                        'open' => 'bg-success',
                                                        'closed' => 'bg-primary',
                                                        'cancelled' => 'bg-danger',
                                                        'refunded' => 'bg-warning text-dark',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp

                                                <span class="badge {{ $badge }}">
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
                    <div class="tab-pane fade" id="tab-tabs">

                        <div class="d-flex justify-content-center px-3 pb-4">

                            <div class="w-100" style="max-width: 1100px;">

                                <div class="table-responsive border rounded-4 bg-white overflow-hidden shadow-sm">

                                    <table class="table align-middle mb-0 coffeeshop-table">

                                        <thead class="table-light">
                                            <tr class="text-muted small">
                                                <th class="ps-3">Tab</th>
                                                <th>Total</th>
                                                <th class="pe-3 text-end">Status</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            @forelse($tabs as $tab)

                                                <tr>

                                                    <td class="ps-3 fw-semibold">
                                                        {{ $tab->tab_name }}
                                                    </td>

                                                    <td class="fw-bold text-primary">
                                                        ₱{{ number_format($tab->total, 2) }}
                                                    </td>

                                                    <td class="pe-3 text-end">
                                                        @php
                                                            $badge = match(strtolower($tab->status)) {
                                                                'closed' => 'bg-secondary',
                                                                'cancelled' => 'bg-danger',
                                                                default => 'bg-primary'
                                                            };
                                                        @endphp

                                                        <span class="badge {{ $badge }}">
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
