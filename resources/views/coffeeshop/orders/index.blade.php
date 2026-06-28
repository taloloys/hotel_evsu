@extends('layouts.app')

@section('title', 'Orders')
@section('pageTitle', 'Order Management')
@section('pageSubtitle', 'Open orders, active tabs, closed, cancelled, and refunded orders')

@section('content')
@include('coffeeshop.partials.alerts')

<style>
#ordersNav .nav-link{
    background:#e9ecef;
    color:#495057;
    border:1px solid #dee2e6;
    font-weight:600;
    box-shadow:0 .125rem .25rem rgba(0,0,0,.05);
    transition:all .2s ease;
}

#ordersNav .nav-link:hover{
    background:#dee2e6;
    color:#212529;
    border-color:#ced4da;
}

#ordersNav .nav-link.active{
    background:#0d6efd;
    color:#fff;
    border-color:#0d6efd;
    box-shadow:0 .25rem .5rem rgba(13,110,253,.25);
}
</style>

{{-- ===================== ORDERS FILTER (TAB STYLE UI ONLY) ===================== --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">

    <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
        <span>Order Management</span>
        <small class="text-muted">All Orders • Active Tabs • Status Tracking</small>
    </div>

    <div class="px-3 pt-3 bg-white border-bottom">

        <ul class="nav nav-pills nav-fill gap-2 fw-semibold" id="ordersNav">

            @foreach([
                'all' => 'All Orders',
                'active_tabs' => 'Active Tabs',
                'closed' => 'Closed',
                'cancelled' => 'Cancelled',
                'refunded' => 'Refunded'
            ] as $key => $label)

                <li class="nav-item">

                    <a href="{{ route('coffeeshop.orders', ['status' => $key]) }}"
                    class="nav-link rounded-pill {{ $status === $key ? 'active' : '' }}">

                        {{ $label }}

                    </a>

                </li>

            @endforeach

        </ul>

    </div>
    <hr>

    {{-- ===================== SEARCH (RIGHT ALIGNED) ===================== --}}
    <div class="d-flex justify-content-end mb-3">

        <form method="GET">

           <div class="input-group" style="width: 450px; border: 1px solid;">
            <span class="input-group-text bg-white">
                <i class="fa-solid fa-magnifying-glass text-muted"></i>
            </span>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   class="form-control"
                   placeholder="Search products..."
                   onkeydown="if(event.key==='Enter'){ this.form.submit(); }">
            </input>
        </div>

        </form>

    </div>

    {{-- ===================== TABLE ===================== --}}
    @if($status === 'active_tabs')

    <div class="card border-0 shadow-sm rounded-4">

        <div class="table-responsive">

            <table class="table mb-0 align-middle">

                <thead class="table-light">
                    <tr>
                        <th>Customer</th>
                        <th>Room</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                @forelse($activeTabs as $tab)

                    <tr>
                        <td class="fw-semibold">{{ $tab->tab_name }}</td>
                        <td>{{ $tab->room?->room_number ?? '—' }}</td>
                        <td>{{ $tab->items->map(fn($i) => $i->product?->name.' x'.$i->quantity)->join(', ') }}</td>
                        <td class="fw-bold text-primary">₱{{ number_format($tab->total, 2) }}</td>
                        <td><span class="badge bg-success">OPEN TAB</span></td>
                        <td>
                            <a href="{{ route('coffeeshop.pos') }}"
                            class="btn btn-sm btn-primary rounded-pill px-3">
                                Manage
                            </a>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="text-muted text-center py-4">
                            No active tabs.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        @if($activeTabs->hasPages())
            <div class="p-3">
                {{ $activeTabs->links() }}
            </div>
        @endif

    </div>

    @else

    <div class="card border-0 shadow-sm rounded-4">

        <div class="table-responsive">

            <table class="table mb-0 align-middle">

                <thead class="table-light">
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Room</th>
                        <th>Payment</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                @forelse($orders as $order)

                    <tr>
                        <td class="fw-semibold">{{ $order->order_number }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->room_number ?? '—' }}</td>
                        <td>{{ $order->payment_method ? str_replace('_', ' ', strtoupper($order->payment_method)) : '—' }}</td>
                        <td class="fw-bold text-primary">₱{{ number_format($order->total, 2) }}</td>
                        <td><span class="badge bg-secondary">{{ strtoupper($order->status) }}</span></td>
                        <td>
                            <a href="{{ route('coffeeshop.orders.show', $order) }}"
                            class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                View
                            </a>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="text-muted text-center py-4">
                            No orders found.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        @if($orders->hasPages())
            <div class="p-3">
                {{ $orders->links() }}
            </div>
        @endif

    </div>

    @endif
</div>

@endsection
