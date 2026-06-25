@extends('layouts.app')

@section('title', 'Tabs')
@section('pageTitle', 'Customer Tabs')
@section('pageSubtitle', 'Manage open, closed, and cancelled tabs')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="d-flex gap-2 mb-3">
    <a href="{{ route('coffeeshop.tabs', ['status' => 'open']) }}" class="btn btn-sm {{ $status === 'open' ? 'btn-primary' : 'btn-outline-secondary' }}">Open</a>
    <a href="{{ route('coffeeshop.tabs', ['status' => 'closed']) }}" class="btn btn-sm {{ $status === 'closed' ? 'btn-primary' : 'btn-outline-secondary' }}">Closed</a>
    <a href="{{ route('coffeeshop.tabs', ['status' => 'cancelled']) }}" class="btn btn-sm {{ $status === 'cancelled' ? 'btn-primary' : 'btn-outline-secondary' }}">Cancelled</a>
    <a href="{{ route('coffeeshop.tabs', ['status' => 'all']) }}" class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
    <a href="{{ route('coffeeshop.pos') }}" class="btn btn-sm btn-success ms-auto">Open POS</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Tab</th><th>Items</th><th>Total</th><th>Status</th><th>Opened</th><th></th></tr></thead>
            <tbody>
            @forelse($tabs as $tab)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $tab->tab_name }}</div>
                        @if($tab->room)<small class="text-muted">Room {{ $tab->room->room_number }}</small>@endif
                    </td>
                    <td>{{ $tab->items->sum('quantity') }}</td>
                    <td>₱{{ number_format($tab->total, 2) }}</td>
                    <td><span class="badge bg-secondary">{{ strtoupper($tab->status) }}</span></td>
                    <td>{{ optional($tab->opened_at)->format('M d, Y H:i') }}</td>
                    <td>
                        @if($tab->status === 'open')
                            <a href="{{ route('coffeeshop.pos') }}" class="btn btn-sm btn-outline-primary">Continue</a>
                        @elseif($tab->status === 'closed' && !$tab->order)
                            <form action="{{ route('coffeeshop.tabs.reopen', $tab) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-outline-warning">Reopen</button></form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted">No tabs found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($tabs->hasPages())<div class="card-footer">{{ $tabs->links() }}</div>@endif
</div>
@endsection
