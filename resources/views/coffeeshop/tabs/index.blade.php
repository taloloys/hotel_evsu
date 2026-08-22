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
                <div class="fw-bold fs-4" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">Tabs</div>
                <div class="opacity-75 mt-1" style="font-size: 0.95rem;">Track every open, paid, and cancelled tab without losing the flow of service.</div>
            </div>
        </div>
    </div>

    <div class="coffeeshop-panel overflow-hidden mb-4">

    {{-- HEADER --}}
    <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center p-3" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif; color: #504538; font-size: 1.1rem;">
        <span>Tabs</span>
        <small class="text-muted fw-normal" style="font-size: 0.85rem;">All Tabs • Open • Closed • Cancelled</small>
    </div>

    {{-- NAV PILLS --}}
    <div class="px-3 pt-3 bg-white border-bottom">

        <ul class="nav nav-pills nav-fill gap-2 fw-semibold coffeeshop-nav-pills" id="tabsNav" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">

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
                    <div class="input-group coffeeshop-form-control" style="width: 420px; border: 1px solid #827567; border-radius: 0.5rem; overflow: hidden;">
                        <span class="input-group-text bg-white border-0"><i class="fa-solid fa-magnifying-glass" style="color: #627e71;"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-0 shadow-none" placeholder="Search tabs..." onkeydown="if(event.key==='Enter'){ this.form.submit(); }" style="font-family: 'Lucida Fax', 'Georgia', serif;">
                    </div>
                </form>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0 coffeeshop-table">

                <thead style="background-color: #f8f3ed; border-bottom: 1px solid #e5e7eb; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">

                    <tr>
                        <th class="ps-4" style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">CUSTOMER TAB</th>
                        <th class="text-center" style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">ITEMS</th>
                        <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">ITEM NOTES</th>
                        <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">TOTAL</th>
                        <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">STATUS</th>
                        <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">OPENED</th>
                        <th></th>
                    </tr>

                </thead>

                <tbody style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">

                @forelse($tabs as $tab)

                    @php
                        $badge = match($tab->status){
                            'open' => 'badge-status-open',
                            'closed' => 'badge-status-closed',
                            'cancelled' => 'bg-danger text-white',
                            default => 'bg-secondary text-white'
                        };
                    @endphp

                    <tr style="border-bottom: 1px solid #f0f0f0;">

                        <td class="ps-4" style="padding: 1.05rem 1rem;">

                            <div style="color: #2c241d; font-weight: 600; font-size: 1.05rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                {{ $tab->tab_name }}
                            </div>

                            <div class="mt-1">
                                @if($tab->tab_type === 'room')
                                    <span class="badge px-2.5 py-1 rounded-pill fw-semibold" style="background-color: #334c42; color: #ffffff; font-size: 0.78rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                        <i class="fa-solid fa-bed me-1"></i>Room Guest
                                    </span>
                                @elseif($tab->tab_type === 'account')
                                    <span class="badge px-2.5 py-1 rounded-pill fw-semibold" style="background-color: #627e71; color: #ffffff; font-size: 0.78rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                        <i class="fa-solid fa-crown me-1" style="color: #c2a889;"></i>Credit Account
                                    </span>
                                @else
                                    <span class="badge px-2.5 py-1 rounded-pill fw-semibold" style="border: 1px solid #c2a889; color: #382e25; background: transparent; font-size: 0.78rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">Walk-in</span>
                                @endif
                            </div>

                            @if($tab->room)
                                <small style="color: #554d46; font-size: 0.85rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;" class="d-block">
                                    Room {{ $tab->room->room_number }}
                                </small>
                            @endif

                        </td>

                        <td class="text-center" style="padding: 1.05rem 1rem; color: #382e25; font-weight: 500; font-size: 0.98rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                            {{ $tab->items->sum('quantity') }}
                        </td>

                        <td style="padding: 1.05rem 1rem;">
                            @if($tab->notes)
                                <span class="text-wrap text-start" style="color: #554d46; font-size: 0.92rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif; max-width: 240px; display: inline-block; line-height: 1.35;">
                                    <i class="fa-solid fa-sticky-note me-1" style="color: #627e71;"></i>{{ $tab->notes }}
                                </span>
                            @else
                                <span style="color: #554d46; font-size: 0.92rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">—</span>
                            @endif
                        </td>

                        <td style="padding: 1.05rem 1rem; color: #2c241d; font-weight: 600; font-size: 1.08rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                            ₱{{ number_format($tab->total, 2) }}
                        </td>

                        <td style="padding: 1.05rem 1rem;">
                            <span class="coffeeshop-pill fw-semibold {{ $badge }}" style="font-size: 0.88rem; padding: 0.28rem 0.8rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                {{ $tab->status === 'closed' ? 'CLOSED' : strtoupper($tab->status) }}
                            </span>
                        </td>

                        <td style="padding: 1.05rem 1rem;">
                            <div style="color: #2c241d; font-weight: 500; font-size: 0.92rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">{{ optional($tab->opened_at)->format('M d, Y') }}</div>
                            <div style="color: #6c757d; font-size: 0.82rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                {{ optional($tab->opened_at)->format('h:i A') }}
                            </div>
                        </td>

                        <td class="text-end pe-4" style="padding: 1.05rem 1rem;">

                            @if($tab->status == 'open')

                                <a href="{{ route('coffeeshop.pos', ['tab_id' => $tab->tab_id]) }}"
                                class="btn text-white btn-sm rounded-pill px-3 fw-semibold" style="background-color: #334c42; border: none; font-size: 0.88rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                    Continue
                                </a>

                            @elseif($tab->status == 'closed' && !$tab->order)

                                <form method="POST"
                                    action="{{ route('coffeeshop.tabs.reopen', $tab) }}"
                                    class="d-inline">
                                    @csrf

                                    <button class="btn btn-warning btn-sm rounded-pill px-3 fw-semibold" style="font-size: 0.88rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
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

                            <div class="fw-bold" style="color: #504538; font-size: 1.1rem; font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">
                                No tabs found
                            </div>

                            <small style="color: #827567; font-size: 0.9rem; font-family: 'Lucida Fax', 'Georgia', serif;">
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
