@extends('layouts.app')

@section('title', 'Tabs')
@section('pageTitle', 'Tabs')
@section('pageSubtitle', 'Manage open, paid, and cancelled tabs')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="coffeeshop-page-shell">
    <div class="coffeeshop-hero">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
            <div>
                <div class="fw-bold fs-5">Tabs</div>
                <div class="opacity-75 mt-1">Track every open, paid, and cancelled tab without losing the flow of service.</div>
            </div>
        </div>
    </div>

    <div class="coffeeshop-panel overflow-hidden mb-4">

    {{-- HEADER --}}
    <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center p-3">
        <span>Tabs</span>
        <small class="text-muted">All Tabs • Open • Closed • Cancelled</small>
    </div>

    {{-- NAV PILLS --}}
    <div class="px-3 pt-3 bg-white border-bottom">

        <ul class="nav nav-pills nav-fill gap-2 fw-semibold coffeeshop-nav-pills" id="tabsNav">

            <li class="nav-item">
                <a href="{{ route('coffeeshop.tabs', ['status' => 'all']) }}"
                class="nav-link rounded-pill {{ $status == 'all' ? 'active' : '' }}">

                    <i class="me-2"></i>
                    All Tabs
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('coffeeshop.tabs', ['status' => 'open']) }}"
                class="nav-link rounded-pill {{ $status == 'open' ? 'active' : '' }}">

                    <i class="me-2"></i>
                    Open
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('coffeeshop.tabs', ['status' => 'closed']) }}"
                class="nav-link rounded-pill {{ $status == 'closed' ? 'active' : '' }}">

                    <i class="me-2"></i>
                    Closed
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('coffeeshop.tabs', ['status' => 'cancelled']) }}"
                class="nav-link rounded-pill {{ $status == 'cancelled' ? 'active' : '' }}">

                    <i class="me-2"></i>
                    Cancelled
                </a>
            </li>

        </ul>

    </div>
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

        {{-- SEARCH --}}
        <div class="p-3 p-lg-4 bg-white border-bottom">
            <div class="d-flex justify-content-end">
                <form method="GET">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div class="input-group coffeeshop-form-control" style="width: 450px; border: 1px solid black; border-radius: 4px;">
                        <span class="input-group-text bg-white border-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-0" placeholder="Search tabs..." onkeydown="if(event.key==='Enter'){ this.form.submit(); }">
                    </div>
                </form>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0 coffeeshop-table">

                <thead class="table-light">

                    <tr>
                        <th class="ps-4">Customer Tab</th>
                        <th class="text-center">Items</th>
                        <th>Item Notes</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Opened</th>
                        <th></th>
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

                            <div class="my-1">
                                @if($tab->tab_type === 'room')
                                    <span class="badge bg-info text-white" style="font-size: 0.7rem;">Room Charge</span>
                                @elseif($tab->tab_type === 'account')
                                    <span class="badge bg-primary text-white" style="font-size: 0.7rem;">Credit Account</span>
                                @else
                                    <span class="badge bg-secondary text-white" style="font-size: 0.7rem;">Walk-in</span>
                                @endif
                            </div>

                            @if($tab->room)
                                <small class="text-muted d-block">
                                    Room {{ $tab->room->room_number }}
                                </small>
                            @endif

                        </td>

                        <td class="text-center">
                            {{ $tab->items->sum('quantity') }}
                        </td>

                        <td>
                            @if($tab->notes)
                                <span class="badge bg-warning-subtle text-dark border border-warning px-2.5 py-1.5 fw-semibold text-wrap text-start" style="font-size: 0.82rem; max-width: 240px; display: inline-block; line-height: 1.35;">
                                    <i class="fa-solid fa-sticky-note me-1 text-warning"></i>{{ $tab->notes }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>

                        <td class="fw-semibold text-primary">
                            ₱{{ number_format($tab->total,2) }}
                        </td>

                        <td>
                            <span class="badge {{ $badge }}">
                                {{ $tab->status === 'closed' ? 'CLOSED' : strtoupper($tab->status) }}
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

                                <a href="{{ route('coffeeshop.pos', ['tab_id' => $tab->tab_id]) }}"
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

                        <td colspan="7" class="text-center py-5">

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
</div>
@endsection
