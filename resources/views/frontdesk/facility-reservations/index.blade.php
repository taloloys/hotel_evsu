@extends('layouts.app')

@section('title', 'Facility Reservations')
@section('pageTitle', 'Facility Reservations')
@section('pageSubtitle', 'Review and manage public facility booking requests')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
    <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <ul class="nav nav-pills card-header-pills">
            <li class="nav-item">
                <a class="nav-link {{ $status === 'all' ? 'active' : 'text-dark' }}" href="{{ route('frontdesk.facility-reservations.index', ['status' => 'all']) }}">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status === 'pending' ? 'active' : 'text-dark' }}" href="{{ route('frontdesk.facility-reservations.index', ['status' => 'pending']) }}">
                    Pending 
                    @if($pendingCount > 0)
                        <span class="badge bg-danger ms-1 rounded-pill">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status === 'approved' ? 'active' : 'text-dark' }}" href="{{ route('frontdesk.facility-reservations.index', ['status' => 'approved']) }}">Approved</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status === 'active' ? 'active' : 'text-dark' }}" href="{{ route('frontdesk.facility-reservations.index', ['status' => 'active']) }}">Active</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status === 'completed' ? 'active' : 'text-dark' }}" href="{{ route('frontdesk.facility-reservations.index', ['status' => 'completed']) }}">Completed</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status === 'cancelled' ? 'active' : 'text-dark' }}" href="{{ route('frontdesk.facility-reservations.index', ['status' => 'cancelled']) }}">Cancelled</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status === 'rejected' ? 'active' : 'text-dark' }}" href="{{ route('frontdesk.facility-reservations.index', ['status' => 'rejected']) }}">Rejected</a>
            </li>
        </ul>

        <a href="{{ route('frontdesk.facility-reservations.create') }}" class="btn btn-sm text-white rounded-pill px-3 shadow-sm" style="background: #334c42;">
            <i class="fa-solid fa-plus me-1"></i> Book Facility
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th class="ps-4">Ref #</th>
                        <th>Booker &amp; Event</th>
                        <th>Facility</th>
                        <th>Schedule</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($reservations as $res)
                    <tr>
                        <td class="ps-4 fw-semibold font-monospace">{{ $res->reference_number }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $res->booker_name }}</div>
                            @if($res->event_name)
                                <div class="text-primary small fw-semibold">
                                    <i class="fa-solid fa-tag me-1"></i>{{ $res->event_name }}
                                </div>
                            @endif
                            <div class="text-muted small">{{ $res->booker_contact }}</div>
                        </td>
                        <td>
                            @if($res->isConsolidated())
                                <div class="text-dark fw-semibold">
                                    {{ $res->facilitySet->name ?? $res->facility_name }}
                                    <span class="badge bg-info text-dark ms-1" style="font-size: 0.65rem;">Set</span>
                                </div>
                                <div class="text-muted small" title="{{ $res->all_facilities->pluck('name')->join(', ') }}">
                                    {{ $res->all_facilities->count() }} spaces &bull; {{ $res->duration_label }}
                                </div>
                            @else
                                <div class="text-dark fw-semibold">{{ $res->facility->name ?? 'Deleted Facility' }}</div>
                                <div class="text-muted small">{{ $res->duration_label }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="text-dark">
                                {{ $res->reservation_date->format('M d, Y') }}
                                @if($res->end_date && $res->end_date->gt($res->reservation_date))
                                    &ndash; {{ $res->end_date->format('M d, Y') }}
                                @endif
                            </div>
                            <div class="text-muted small">
                                {{ \Carbon\Carbon::parse($res->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($res->end_time)->format('h:i A') }}
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-success">
                                ₱{{ number_format($res->effective_amount, 2) }}
                            </div>
                            @if($res->agreed_rate !== null)
                                <span class="badge bg-warning text-dark font-monospace" style="font-size: 0.65rem;">
                                    Agreed: ₱{{ number_format($res->agreed_rate, 2) }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($res->status === 'pending')
                                <span class="badge bg-warning text-dark border border-warning shadow-sm px-3 rounded-pill uppercase tracking-wider">Pending</span>
                            @elseif($res->status === 'approved')
                                <span class="badge bg-success shadow-sm px-3 rounded-pill uppercase tracking-wider">Approved</span>
                            @elseif($res->status === 'active')
                                <span class="badge bg-primary shadow-sm px-3 rounded-pill uppercase tracking-wider">Active</span>
                            @elseif($res->status === 'completed')
                                <span class="badge bg-info text-dark shadow-sm px-3 rounded-pill uppercase tracking-wider">Completed</span>
                            @elseif($res->status === 'cancelled')
                                <span class="badge bg-secondary shadow-sm px-3 rounded-pill uppercase tracking-wider">Cancelled</span>
                            @else
                                <span class="badge bg-danger shadow-sm px-3 rounded-pill uppercase tracking-wider">Rejected</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('frontdesk.facility-reservations.show', $res) }}" class="btn btn-sm btn-outline-primary rounded-pill shadow-sm fw-semibold">
                                Manage <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fa-regular fa-calendar-xmark fs-2 mb-3"></i>
                            <p class="mb-0">No reservations found in this category.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($reservations->hasPages())
    <div class="card-footer bg-white p-3 border-top-0">
        {{ $reservations->links() }}
    </div>
    @endif
</div>
@endsection
