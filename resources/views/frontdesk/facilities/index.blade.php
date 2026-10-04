@extends('layouts.app')

@section('title', 'Manage Facilities')
@section('pageTitle', 'Facilities')
@section('pageSubtitle', 'Manage individual spaces and consolidated sets available for reservation')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- QUICK STATS --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4" style="border:1px solid #c2a889 !important;">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:52px;height:52px;background:#e8f0ec;color:#334c42;">
                    <i class="fa-solid fa-building fs-4"></i>
                </div>
                <div>
                    <div class="small fw-bold" style="color:#1a1a1a;">Individual Facilities</div>
                    <h3 class="fw-bold mb-0 mt-1" style="color:#1a1a1a;font-size:1.85rem;">{{ $facilities->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4" style="border:1px solid #c2a889 !important;">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:52px;height:52px;background:#fff3cd;color:#92400e;">
                    <i class="fa-solid fa-layer-group fs-4"></i>
                </div>
                <div>
                    <div class="small fw-bold" style="color:#1a1a1a;">Consolidated Sets</div>
                    <h3 class="fw-bold mb-0 mt-1" style="color:#1a1a1a;font-size:1.85rem;">{{ $facilitySets->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <a href="{{ route('frontdesk.facility-reservations.index', ['status' => 'pending']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4" style="border:1px solid #c2a889 !important;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:52px;height:52px;background:#fff3cd;color:#92400e;">
                        <i class="fa-solid fa-clock fs-4"></i>
                    </div>
                    <div>
                        <div class="small fw-bold" style="color:#1a1a1a;">Pending Reservations</div>
                        <h3 class="fw-bold mb-0 mt-1" style="color:#1a1a1a;font-size:1.85rem;">{{ $pendingCount }}</h3>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- ── Individual Facilities ──────────────────────────────────────────────── --}}
<div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4" style="border:1px solid #c2a889 !important;">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 px-4" style="border-bottom:1px solid #f0e8de;">
        <h5 class="mb-0 fw-bold" style="color:#1a1a1a;">
            <i class="fa-solid fa-building me-2" style="color:#334c42;"></i>Individual Facilities
        </h5>
        <div class="d-flex gap-2">
            <a href="{{ route('frontdesk.facility-reservations.index') }}"
               class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm fw-semibold">
                <i class="fa-solid fa-calendar-check me-1"></i> Reservations
            </a>
            <a href="{{ route('frontdesk.facilities.create', ['type' => 'single']) }}"
               class="btn btn-sm rounded-pill fw-semibold shadow-sm text-white"
               style="background:#334c42;border-color:#334c42;">
                <i class="fa-solid fa-plus me-1"></i> Add Facility
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#faf6f0;border-bottom:2px solid #c2a889;">
                    <tr class="small fw-bold" style="color:#1a1a1a;">
                        <th class="ps-4">Image</th>
                        <th>Facility Name</th>
                        <th>Hourly Rate</th>
                        <th>Daily Rate</th>
                        <th>Capacity</th>
                        <th>Reservations</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($facilities as $facility)
                    <tr style="border-bottom:1px solid #f0f0f0;">
                        <td class="ps-4">
                            @if(!empty($facility->images) && count($facility->images) > 0)
                                <img src="{{ \App\Models\Facility::imageUrl($facility->images[0]) }}"
                                     class="rounded-3 shadow-sm"
                                     style="width:64px;height:48px;object-fit:cover;"
                                     alt="{{ $facility->name }}">
                            @else
                                <div class="rounded-3 bg-light d-flex align-items-center justify-content-center text-muted"
                                     style="width:64px;height:48px;">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold" style="color:#1a1a1a;">{{ $facility->name }}</div>
                            @if($facility->description)
                                <div class="small text-muted text-truncate" style="max-width:240px;">{{ $facility->description }}</div>
                            @endif
                            @if($facility->facilitySets->isNotEmpty())
                                <div class="mt-1">
                                    @foreach($facility->facilitySets as $fs)
                                        <span class="badge bg-info text-dark rounded-pill px-2" style="font-size:.68rem;">Part of {{ $fs->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($facility->effective_hourly_rate)
                                <span class="fw-semibold text-success">₱{{ number_format($facility->effective_hourly_rate, 2) }}</span>
                                <span class="badge ms-1 rounded-pill px-2" style="background:#dbeafe;color:#1e40af;font-size:.7rem;">/hr</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @if($facility->effective_daily_rate)
                                <span class="fw-semibold text-success">₱{{ number_format($facility->effective_daily_rate, 2) }}</span>
                                <span class="badge ms-1 rounded-pill px-2" style="background:#f3e8ff;color:#7c3aed;font-size:.7rem;">/day</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $facility->capacity ? $facility->capacity . ' pax' : 'N/A' }}
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3" style="background:#e8f0ec;color:#334c42;font-weight:600;">
                                {{ $facility->reservations_count }}
                            </span>
                        </td>
                        <td>
                            @if($facility->is_active)
                                <span class="badge rounded-pill px-3 py-1 bg-success-subtle text-success fw-semibold" style="font-size:.8rem;">Active</span>
                            @else
                                <span class="badge rounded-pill px-3 py-1 bg-secondary-subtle text-secondary fw-semibold" style="font-size:.8rem;">Disabled</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('frontdesk.facilities.edit', $facility->facility_id) }}"
                               class="btn btn-sm btn-outline-primary rounded-circle me-1" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('frontdesk.facilities.toggle', $facility->facility_id) }}"
                                  method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="btn btn-sm rounded-circle {{ $facility->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                        title="{{ $facility->is_active ? 'Disable' : 'Enable' }}">
                                    <i class="fa-solid {{ $facility->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fa-solid fa-building fs-1 mb-3 d-block" style="color:#c2a889;"></i>
                            <p class="fw-semibold mb-1">No individual facilities yet</p>
                            <a href="{{ route('frontdesk.facilities.create') }}"
                               class="btn btn-sm rounded-pill px-4 fw-semibold text-white shadow-sm mt-2"
                               style="background:#334c42;">
                                <i class="fa-solid fa-plus me-1"></i> Add First Facility
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Consolidated Facility Sets ────────────────────────────────────────── --}}
<div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="border:1px solid #c2a889 !important;">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 px-4" style="border-bottom:1px solid #f0e8de;">
        <h5 class="mb-0 fw-bold" style="color:#1a1a1a;">
            <i class="fa-solid fa-layer-group me-2" style="color:#92400e;"></i>Consolidated Facility Sets
        </h5>
        <a href="{{ route('frontdesk.facilities.create', ['type' => 'set']) }}"
           class="btn btn-sm rounded-pill fw-semibold shadow-sm text-dark btn-warning">
            <i class="fa-solid fa-layer-group me-1"></i> Add Facility Set
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#faf6f0;border-bottom:2px solid #c2a889;">
                    <tr class="small fw-bold" style="color:#1a1a1a;">
                        <th class="ps-4">Image</th>
                        <th>Set Name</th>
                        <th>Includes</th>
                        <th>Hourly Rate</th>
                        <th>Daily Rate</th>
                        <th>Capacity</th>
                        <th>Reservations</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($facilitySets as $set)
                    <tr style="border-bottom:1px solid #f0f0f0;">
                        <td class="ps-4">
                            @if(!empty($set->images) && count($set->images) > 0)
                                <img src="{{ \App\Models\FacilitySet::imageUrl($set->images[0]) }}"
                                     class="rounded-3 shadow-sm"
                                     style="width:64px;height:48px;object-fit:cover;"
                                     alt="{{ $set->name }}">
                            @else
                                <div class="rounded-3 bg-light d-flex align-items-center justify-content-center text-muted"
                                     style="width:64px;height:48px;">
                                    <i class="fa-solid fa-layer-group"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold" style="color:#1a1a1a;">{{ $set->name }}</div>
                            @if($set->prefix_code)
                                <div class="small text-muted">Prefix: <code>{{ $set->prefix_code }}</code></div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($set->facilities as $member)
                                    <span class="badge rounded-pill px-2" style="background:#e8f0ec;color:#334c42;font-size:.72rem;">
                                        {{ $member->name }}
                                    </span>
                                @endforeach
                                @if($set->facilities->isEmpty())
                                    <span class="text-muted small fst-italic">None</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($set->effective_hourly_rate)
                                <span class="fw-semibold text-success">₱{{ number_format($set->effective_hourly_rate, 2) }}</span>
                                <span class="badge ms-1 rounded-pill px-2" style="background:#dbeafe;color:#1e40af;font-size:.7rem;">/hr</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @if($set->effective_daily_rate)
                                <span class="fw-semibold text-success">₱{{ number_format($set->effective_daily_rate, 2) }}</span>
                                <span class="badge ms-1 rounded-pill px-2" style="background:#f3e8ff;color:#7c3aed;font-size:.7rem;">/day</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $set->resolved_capacity ? $set->resolved_capacity . ' pax' : 'N/A' }}
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3" style="background:#e8f0ec;color:#334c42;font-weight:600;">
                                {{ $set->reservations_count }}
                            </span>
                        </td>
                        <td>
                            @if($set->is_active)
                                <span class="badge rounded-pill px-3 py-1 bg-success-subtle text-success fw-semibold" style="font-size:.8rem;">Active</span>
                            @else
                                <span class="badge rounded-pill px-3 py-1 bg-secondary-subtle text-secondary fw-semibold" style="font-size:.8rem;">Disabled</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('frontdesk.facilities.edit', $set->facility_set_id) }}?type=set"
                               class="btn btn-sm btn-outline-primary rounded-circle me-1" title="Edit Set">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('frontdesk.facilities.toggle', $set->facility_set_id) }}?type=set"
                                  method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="btn btn-sm rounded-circle {{ $set->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                        title="{{ $set->is_active ? 'Disable' : 'Enable' }}">
                                    <i class="fa-solid {{ $set->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fa-solid fa-layer-group fs-1 mb-3 d-block" style="color:#c2a889;"></i>
                            <p class="fw-semibold mb-1">No consolidated sets configured yet</p>
                            <p class="text-muted small mb-3">Group multiple spaces into one bookable unit.</p>
                            <a href="{{ route('frontdesk.facilities.create') }}"
                               class="btn btn-sm rounded-pill px-4 fw-semibold text-white shadow-sm"
                               style="background:#334c42;">
                                <i class="fa-solid fa-plus me-1"></i> Create a Set
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
