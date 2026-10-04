@extends('layouts.app')

@section('title', 'Manage Facilities')
@section('pageTitle', 'Manage Facilities')
@section('pageSubtitle', 'Configure individual spaces and consolidated facility sets for public booking')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ── Individual Facilities ──────────────────────────────────────────────── --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 text-dark fw-bold">
            <i class="fa-solid fa-building me-2 text-primary"></i>Individual Facilities
            <span class="badge bg-secondary ms-2 rounded-pill" style="font-size:.75rem;">{{ $facilities->count() }}</span>
        </h5>
        <a href="{{ route('admin.facilities.create', ['type' => 'single']) }}" class="btn btn-primary btn-sm rounded-pill fw-semibold shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Add Facility
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small">
                    <tr>
                        <th class="ps-4" style="width:80px;">Image</th>
                        <th>Name</th>
                        <th>Hourly Rate</th>
                        <th>Daily Rate</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($facilities as $facility)
                    <tr>
                        <td class="ps-4">
                            @if(!empty($facility->images) && count($facility->images) > 0)
                                <img src="{{ \App\Models\Facility::imageUrl($facility->images[0]) }}"
                                     class="rounded shadow-sm" style="width:60px;height:45px;object-fit:cover;"
                                     alt="{{ $facility->name }}">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted"
                                     style="width:60px;height:45px;">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $facility->name }}</div>
                            @if($facility->facilitySets->isNotEmpty())
                                <div class="small text-muted mt-1">
                                    Part of:
                                    @foreach($facility->facilitySets as $fs)
                                        <span class="badge bg-info text-dark rounded-pill px-2" style="font-size:.7rem;">{{ $fs->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($facility->effective_hourly_rate)
                                <span class="fw-semibold text-success">₱{{ number_format($facility->effective_hourly_rate, 2) }}</span>
                                <span class="badge ms-1 rounded-pill px-2" style="background:#dbeafe;color:#1e40af;font-size:.7rem;">/ hr</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @if($facility->effective_daily_rate)
                                <span class="fw-semibold text-success">₱{{ number_format($facility->effective_daily_rate, 2) }}</span>
                                <span class="badge ms-1 rounded-pill px-2" style="background:#f3e8ff;color:#7c3aed;font-size:.7rem;">/ day</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $facility->capacity ? $facility->capacity . ' pax' : 'N/A' }}
                        </td>
                        <td>
                            @if($facility->is_active)
                                <span class="badge rounded-pill px-3 py-1 bg-success-subtle text-success fw-semibold" style="font-size:.8rem;">Active</span>
                            @else
                                <span class="badge rounded-pill px-3 py-1 bg-secondary-subtle text-secondary fw-semibold" style="font-size:.8rem;">Disabled</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.facilities.edit', $facility->facility_id) }}"
                               class="btn btn-sm btn-outline-primary rounded-circle me-1" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('admin.facilities.toggle', $facility->facility_id) }}"
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
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fa-solid fa-folder-open fs-2 mb-3 d-block"></i>
                            No facilities found. <a href="{{ route('admin.facilities.create') }}">Create one</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Consolidated Facility Sets ────────────────────────────────────────── --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 text-dark fw-bold">
            <i class="fa-solid fa-layer-group me-2 text-warning"></i>Consolidated Facility Sets
            <span class="badge bg-warning text-dark ms-2 rounded-pill" style="font-size:.75rem;">{{ $facilitySets->count() }}</span>
        </h5>
        <a href="{{ route('admin.facilities.create', ['type' => 'set']) }}" class="btn btn-warning btn-sm rounded-pill fw-semibold shadow-sm text-dark">
            <i class="fa-solid fa-layer-group me-1"></i> Add Facility Set
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small">
                    <tr>
                        <th class="ps-4" style="width:80px;">Image</th>
                        <th>Set Name</th>
                        <th>Includes</th>
                        <th>Hourly Rate</th>
                        <th>Daily Rate</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($facilitySets as $set)
                    <tr>
                        <td class="ps-4">
                            @if(!empty($set->images) && count($set->images) > 0)
                                <img src="{{ \App\Models\FacilitySet::imageUrl($set->images[0]) }}"
                                     class="rounded shadow-sm" style="width:60px;height:45px;object-fit:cover;"
                                     alt="{{ $set->name }}">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted"
                                     style="width:60px;height:45px;">
                                    <i class="fa-solid fa-layer-group"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $set->name }}</div>
                            @if($set->prefix_code)
                                <div class="small text-muted">Ref prefix: <code>{{ $set->prefix_code }}</code></div>
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
                                    <span class="text-muted small fst-italic">None assigned</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($set->effective_hourly_rate)
                                <span class="fw-semibold text-success">₱{{ number_format($set->effective_hourly_rate, 2) }}</span>
                                <span class="badge ms-1 rounded-pill px-2" style="background:#dbeafe;color:#1e40af;font-size:.7rem;">/ hr</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @if($set->effective_daily_rate)
                                <span class="fw-semibold text-success">₱{{ number_format($set->effective_daily_rate, 2) }}</span>
                                <span class="badge ms-1 rounded-pill px-2" style="background:#f3e8ff;color:#7c3aed;font-size:.7rem;">/ day</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $set->resolved_capacity ? $set->resolved_capacity . ' pax' : 'N/A' }}
                        </td>
                        <td>
                            @if($set->is_active)
                                <span class="badge rounded-pill px-3 py-1 bg-success-subtle text-success fw-semibold" style="font-size:.8rem;">Active</span>
                            @else
                                <span class="badge rounded-pill px-3 py-1 bg-secondary-subtle text-secondary fw-semibold" style="font-size:.8rem;">Disabled</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.facilities.edit', $set->facility_set_id) }}?type=set"
                               class="btn btn-sm btn-outline-primary rounded-circle me-1" title="Edit Set">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('admin.facilities.toggle', $set->facility_set_id) }}?type=set"
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
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="fa-solid fa-layer-group fs-2 mb-3 d-block"></i>
                            No consolidated sets configured yet.
                            <a href="{{ route('admin.facilities.create') }}">Create a facility set</a> to group multiple spaces.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
