@extends('layouts.app')

@section('title', 'Customers')
@section('pageTitle', 'Customer History')
@section('pageSubtitle', 'Order history, frequent customers, open and closed tabs')

@section('content')
@include('coffeeshop.partials.alerts')


<div class="card border-0 shadow-sm">

    <div class="card-body">

        {{-- ===================== CLEAN STATS HEADER ===================== --}}
        <div class="row g-0 mb-4 text-center border rounded-3 overflow-hidden bg-white">

            <div class="col-md-3 col-6 py-4 border-end border-bottom">
                <div class="text-muted meduim">Total Customers</div>
                <div class="fw-bold fs-5 text-primary">
                    {{ $orders->pluck('customer_name')->unique()->count() }}
                </div>
            </div>

            <div class="col-md-3 col-6 py-3 border-end border-bottom">
                <div class="text-muted medium">Active Tabs</div>
                <div class="fw-bold fs-5 text-info">
                    {{ $tabs->where('status', 'open')->count() }}
                </div>
            </div>

            <div class="col-md-3 col-6 py-3 border-end">
                <div class="text-muted medium">Completed</div>
                <div class="fw-bold fs-5 text-success">
                    {{ $orders->where('status', 'closed')->count() }}
                </div>
            </div>

            <div class="col-md-3 col-6 py-3">
                <div class="text-muted medium">Refunded</div>
                <div class="fw-bold fs-5 text-danger">
                    {{ $orders->where('status', 'refunded')->count() }}
                </div>
            </div>

        </div>

        {{-- ===================== FILTER (RIGHT ALIGNED) ===================== --}}
        <div class="d-flex justify-content-end mb-4">

            <form method="GET">

                <div class="d-flex gap-2 flex-wrap align-items-center">

                    {{-- SEARCH --}}
                    <div class="input-group" style="width: 450px; border: 1px solid;">
                        <span class="input-group-text bg-white">
                            <i class="fa-solid fa-magnifying-glass text-muted"></i>
                        </span>

                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="Search customer or order..."
                            onkeydown="if(event.key==='Enter'){ this.form.submit(); }">
                    </div>

                    {{-- STATUS FILTER --}}
                    <select name="status"
                            class="form-select"
                            style="width: 220px; border: 1px solid;"
                            onchange="this.form.submit()">

                        <option value="">All Records</option>

                        <option value="open" @selected($status === 'open')>
                            Open Tabs
                        </option>

                        <option value="closed" @selected($status === 'closed')>
                            Closed Orders
                        </option>

                        <option value="cancelled" @selected($status === 'cancelled')>
                            Cancelled
                        </option>

                        <option value="refunded" @selected($status === 'refunded')>
                            Refunded
                        </option>

                    </select>

                </div>

            </form>

        </div>

        {{-- ===================== CONTENT (ORDER-STYLE TABS) ===================== --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            <div class="card-body">

                {{-- TAB NAVIGATION (MATCHING ORDERS STYLE) --}}
                <ul class="nav nav-pills nav-fill gap-2 bg-light p-2 rounded-3 mb-3">

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

                {{-- TAB CONTENT --}}
                <div class="tab-content">

                    {{-- ===================== FREQUENT CUSTOMERS ===================== --}}
                    <div class="tab-pane fade show active" id="tab-customers">

                        @forelse($frequentCustomers as $customer)
                            <div class="d-flex justify-content-between py-2 border-bottom small">

                                <div>
                                    <div class="fw-semibold">{{ $customer->customer_name }}</div>
                                    <div class="text-muted">{{ $customer->order_count }} orders</div>
                                </div>

                                <div class="fw-semibold">
                                    ₱{{ number_format($customer->total_spent, 2) }}
                                </div>

                            </div>
                        @empty
                            <div class="text-muted small">No customer data yet.</div>
                        @endforelse

                    </div>

                    {{-- ===================== ORDERS ===================== --}}
                    <div class="tab-pane fade" id="tab-orders">

                        <div class="table-responsive">

                            <table class="table table-sm align-middle mb-0">

                                <thead class="text-muted small bg-light">
                                    <tr>
                                        <th>Order</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($orders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('coffeeshop.orders.show', $order) }}"
                                                    class="fw-semibold text-decoration-none">
                                                    {{ $order->order_number }}
                                                </a>
                                            </td>
                                            <td>{{ $order->customer_name }}</td>
                                            <td>₱{{ number_format($order->total, 2) }}</td>
                                            <td>{{ strtoupper($order->status) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-muted text-center py-3">
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

                        <div class="table-responsive">

                            <table class="table table-sm align-middle mb-0">

                                <thead class="text-muted small bg-light">
                                    <tr>
                                        <th>Tab</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($tabs as $tab)
                                        <tr>
                                            <td>{{ $tab->tab_name }}</td>
                                            <td>₱{{ number_format($tab->total, 2) }}</td>
                                            <td>{{ strtoupper($tab->status) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-muted text-center py-3">
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
@endsection
