@extends('layouts.app')

@section('title', 'Tabs')
@section('pageTitle', 'Customer Tabs')
@section('pageSubtitle', 'Manage open, closed, and cancelled tabs')

@section('content')
@include('coffeeshop.partials.alerts')

<style>
#tabsNav .nav-link{
    background:#e9ecef;
    color:#495057;
    border:1px solid #dee2e6;
    font-weight:600;
    box-shadow:0 .125rem .25rem rgba(0,0,0,.05);
    transition:all .2s ease;
}

#tabsNav .nav-link:hover{
    background:#dee2e6;
    color:#212529;
    border-color:#ced4da;
}

#tabsNav .nav-link.active{
    background:#0d6efd;
    color:#fff;
    border-color:#0d6efd;
    box-shadow:0 .25rem .5rem rgba(13,110,253,.25);
}
</style>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">

    {{-- HEADER --}}
    <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
        <span>Tab Management</span>
        <small class="text-muted">Open • Closed • Cancelled • All Tabs</small>
    </div>

    {{-- NAV PILLS --}}
    <div class="px-3 pt-3 bg-white border-bottom">

        <ul class="nav nav-pills nav-fill gap-2 fw-semibold" id="tabsNav">

            <li class="nav-item">
                <a href="{{ route('coffeeshop.tabs', ['status' => 'open']) }}"
                class="nav-link rounded-pill {{ $status == 'open' ? 'active' : '' }}">

                    <i class="fa-solid fa-circle-play me-2"></i>
                    Open
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('coffeeshop.tabs', ['status' => 'closed']) }}"
                class="nav-link rounded-pill {{ $status == 'closed' ? 'active' : '' }}">

                    <i class="fa-solid fa-circle-check me-2"></i>
                    Closed
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('coffeeshop.tabs', ['status' => 'cancelled']) }}"
                class="nav-link rounded-pill {{ $status == 'cancelled' ? 'active' : '' }}">

                    <i class="fa-solid fa-ban me-2"></i>
                    Cancelled
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('coffeeshop.tabs', ['status' => 'all']) }}"
                class="nav-link rounded-pill {{ $status == 'all' ? 'active' : '' }}">

                    <i class="fa-solid fa-table-list me-2"></i>
                    All Tabs
                </a>
            </li>

        </ul>

    </div>
    <hr>
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

        {{-- TABS --}}
        <div class="bg-light px-3 pt-3">

            <!-- Your nav pills here -->

        </div>

        {{-- TABLE --}}
        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                <tr>
                    <th class="ps-4">Customer Tab</th>
                    <th class="text-center">Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Opened</th>
                </tr>

                </thead>

                <tbody>

                @forelse($tabs as $tab)

                    @php
                        $badge = match($tab->status){
                            'open' => 'bg-primary',
                            'closed' => 'bg-success',
                            'cancelled' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                    @endphp

                    <tr>

                        <td class="ps-4">

                            <div class="fw-semibold">
                                {{ $tab->tab_name }}
                            </div>

                            @if($tab->room)
                                <small class="text-muted">
                                    Room {{ $tab->room->room_number }}
                                </small>
                            @endif

                        </td>

                        <td class="text-center">
                            {{ $tab->items->sum('quantity') }}
                        </td>

                        <td class="fw-semibold text-primary">
                            ₱{{ number_format($tab->total,2) }}
                        </td>

                        <td>
                            <span class="badge {{ $badge }}">
                                {{ strtoupper($tab->status) }}
                            </span>
                        </td>

                        <td>
                            {{ optional($tab->opened_at)->format('M d, Y') }}
                            <br>
                            <small class="text-muted">
                                {{ optional($tab->opened_at)->format('h:i A') }}
                            </small>
                        </td>

                        <td class="text-end pe-4">

                            @if($tab->status == 'open')

                                <a href="{{ route('coffeeshop.pos') }}"
                                class="btn btn-primary btn-sm rounded-pill px-3">
                                    Continue
                                </a>

                            @elseif($tab->status == 'closed' && !$tab->order)

                                <form method="POST"
                                    action="{{ route('coffeeshop.tabs.reopen',$tab) }}"
                                    class="d-inline">
                                    @csrf

                                    <button class="btn btn-warning btn-sm rounded-pill px-3">
                                        Reopen
                                    </button>

                                </form>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="text-center py-5">

                            <i class="fa-solid fa-folder-open fa-3x text-muted mb-3"></i>

                            <div class="fw-semibold">
                                No tabs found
                            </div>

                            <small class="text-muted">
                                Customer tabs will appear here.
                            </small>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        @if($tabs->hasPages())

            <div class="card-footer bg-white border-0 py-3">
                {{ $tabs->links() }}
            </div>

        @endif

    </div>
</div>
@endsection
