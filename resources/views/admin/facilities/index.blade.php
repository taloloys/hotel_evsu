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
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fw-bold" style="color: #1a1a1a;">
            <i class="fa-solid fa-building me-2" style="color: #627e71;"></i>Individual Facilities
            <span class="badge ms-2" style="background-color: #f3ede4; color: #3d332a;">{{ $facilities->total() }}</span>
        </h5>
        <a href="{{ route('admin.facilities.create', ['type' => 'single']) }}" class="btn text-white px-3 d-flex align-items-center gap-2 fw-semibold shadow-sm" style="height: 40px; background-color: #334c42; border: none; border-radius: 0.5rem; font-size: 0.9rem;">
            <i class="fa-solid fa-plus"></i> Add Facility
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #f8f3ed; border-bottom: 2px solid #c2a889;">
                    <tr class="small fw-bold">
                        <th class="ps-4" style="color: #2c241d;">IMAGE</th>
                        <th style="color: #2c241d;">NAME</th>
                        <th style="color: #2c241d;">HOURLY RATE</th>
                        <th style="color: #2c241d;">DAILY RATE</th>
                        <th style="color: #2c241d;">CAPACITY</th>
                        <th style="color: #2c241d;">STATUS</th>
                        <th class="text-end pe-4" style="color: #2c241d;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($facilities as $facility)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td class="ps-4">
                            @if(!empty($facility->images) && count($facility->images) > 0)
                                <img src="{{ \App\Models\Facility::imageUrl($facility->images[0]) }}"
                                     class="rounded shadow-sm border" style="width:60px;height:45px;object-fit:cover; border-color: #c2a889 !important;"
                                     alt="{{ $facility->name }}">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted border"
                                     style="width:60px;height:45px; border-color: #e2d3be !important; background-color: #f8f3ed !important;">
                                    <i class="fa-solid fa-image" style="color: #c2a889;"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold" style="color: #1a1a1a;">{{ $facility->name }}</div>
                            @if($facility->facilitySets->isNotEmpty())
                                <div class="small mt-1" style="color: #627e71;">
                                    <span class="me-1">Part of:</span>
                                    @foreach($facility->facilitySets as $fs)
                                        <span class="badge fw-semibold" style="background-color: #e8f0ec; color: #1e332b; border: 1px solid #627e71; font-size: 0.65rem;">{{ $fs->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($facility->effective_hourly_rate)
                                <span class="fw-bold" style="color: #334c42;">₱{{ number_format($facility->effective_hourly_rate, 2) }}</span>
                                <span class="badge ms-1 px-2" style="background-color: #f3ede4; color: #627e71; border: 1px solid #e2d3be; font-size: 0.7rem;">/ hr</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @if($facility->effective_daily_rate)
                                <span class="fw-bold" style="color: #334c42;">₱{{ number_format($facility->effective_daily_rate, 2) }}</span>
                                <span class="badge ms-1 px-2" style="background-color: #f3ede4; color: #627e71; border: 1px solid #e2d3be; font-size: 0.7rem;">/ day</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td style="color: #3d332a;">
                            {{ $facility->capacity ? $facility->capacity . ' pax' : 'N/A' }}
                        </td>
                        <td>
                            @if($facility->is_active)
                                <span class="badge-status badge-status-active">Active</span>
                            @else
                                <span class="badge-status badge-status-maintenance">Disabled</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.facilities.edit', $facility->facility_id) }}?type=single"
                               class="btn btn-sm shadow-sm me-1" style="border: 1px solid #c2a889; background-color: #f8f3ed; color: #3d332a;" title="Edit Facility">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('admin.facilities.toggle', $facility->facility_id) }}?type=single"
                                  method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="btn btn-sm shadow-sm" style="border: 1px solid #c2a889; background-color: {{ $facility->is_active ? '#ffffff' : '#e8f0ec' }}; color: {{ $facility->is_active ? '#999' : '#1e332b' }};"
                                        title="{{ $facility->is_active ? 'Disable' : 'Enable' }}">
                                    <i class="fa-solid {{ $facility->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5" style="color: #627e71;">
                            <i class="fa-solid fa-folder-open fs-2 mb-3 d-block" style="color: #c2a889;"></i>
                            No facilities found. <a href="{{ route('admin.facilities.create') }}" style="color: #334c42; font-weight: bold; text-decoration: underline;">Create one</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex align-items-center justify-content-between p-3" style="background-color: #fcfbf9; border-top: 1px solid #e2d3be;">
            <span class="text-muted small" style="color: #627e71 !important;">
                Showing <span class="badge" style="background-color: #f3ede4; color: #3d332a; border: 1px solid #e2d3be;">{{ $facilities->firstItem() ?? 0 }}-{{ $facilities->lastItem() ?? 0 }}</span> of <strong>{{ $facilities->total() }}</strong>
            </span>
            <div class="m-0">
                {{ $facilities->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

{{-- ── Consolidated Facility Sets ─────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fw-bold" style="color: #1a1a1a;">
            <i class="fa-solid fa-layer-group me-2" style="color: #c2a889;"></i>Consolidated Facility Sets
            <span class="badge ms-2" style="background-color: #f3ede4; color: #3d332a;">{{ $facilitySets->total() }}</span>
        </h5>
        <a href="{{ route('admin.facilities.create', ['type' => 'set']) }}" class="btn text-white px-3 d-flex align-items-center gap-2 fw-semibold shadow-sm" style="height: 40px; background-color: #c2a889; border: none; border-radius: 0.5rem; font-size: 0.9rem; color: #2c241d !important;">
            <i class="fa-solid fa-plus"></i> Add Facility Set
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #f8f3ed; border-bottom: 2px solid #c2a889;">
                    <tr class="small fw-bold">
                        <th class="ps-4" style="color: #2c241d;">IMAGE</th>
                        <th style="color: #2c241d;">SET NAME</th>
                        <th style="color: #2c241d;">INCLUDES</th>
                        <th style="color: #2c241d;">HOURLY RATE</th>
                        <th style="color: #2c241d;">DAILY RATE</th>
                        <th style="color: #2c241d;">CAPACITY</th>
                        <th style="color: #2c241d;">STATUS</th>
                        <th class="text-end pe-4" style="color: #2c241d;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($facilitySets as $set)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td class="ps-4">
                            @if(!empty($set->images) && count($set->images) > 0)
                                <img src="{{ \App\Models\FacilitySet::imageUrl($set->images[0]) }}"
                                     class="rounded shadow-sm border" style="width:60px;height:45px;object-fit:cover; border-color: #c2a889 !important;"
                                     alt="{{ $set->name }}">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted border"
                                     style="width:60px;height:45px; border-color: #e2d3be !important; background-color: #f8f3ed !important;">
                                    <i class="fa-solid fa-layer-group" style="color: #c2a889;"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold" style="color: #1a1a1a;">{{ $set->name }}</div>
                            @if($set->prefix_code)
                                <div class="small mt-1" style="color: #627e71;">Ref prefix: <code style="color: #c2a889; background-color: #f8f3ed; padding: 2px 6px; border-radius: 4px; border: 1px solid #e2d3be;">{{ $set->prefix_code }}</code></div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($set->facilities as $member)
                                    <span class="badge fw-semibold" style="background-color: #f8f3ed; color: #3d332a; border: 1px solid #c2a889; font-size: 0.65rem;">
                                        {{ $member->name }}
                                    </span>
                                @endforeach
                                @if($set->facilities->isEmpty())
                                    <span class="small fst-italic" style="color: #a0a0a0;">None assigned</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($set->effective_hourly_rate)
                                <span class="fw-bold" style="color: #334c42;">₱{{ number_format($set->effective_hourly_rate, 2) }}</span>
                                <span class="badge ms-1 px-2" style="background-color: #f3ede4; color: #627e71; border: 1px solid #e2d3be; font-size: 0.7rem;">/ hr</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @if($set->effective_daily_rate)
                                <span class="fw-bold" style="color: #334c42;">₱{{ number_format($set->effective_daily_rate, 2) }}</span>
                                <span class="badge ms-1 px-2" style="background-color: #f3ede4; color: #627e71; border: 1px solid #e2d3be; font-size: 0.7rem;">/ day</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td style="color: #3d332a;">
                            {{ $set->resolved_capacity ? $set->resolved_capacity . ' pax' : 'N/A' }}
                        </td>
                        <td>
                            @if($set->is_active)
                                <span class="badge-status badge-status-active">Active</span>
                            @else
                                <span class="badge-status badge-status-maintenance">Disabled</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.facilities.edit', $set->facility_set_id) }}?type=set"
                               class="btn btn-sm shadow-sm me-1" style="border: 1px solid #c2a889; background-color: #f8f3ed; color: #3d332a;" title="Edit Set">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('admin.facilities.toggle', $set->facility_set_id) }}?type=set"
                                  method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="btn btn-sm shadow-sm" style="border: 1px solid #c2a889; background-color: {{ $set->is_active ? '#ffffff' : '#e8f0ec' }}; color: {{ $set->is_active ? '#999' : '#1e332b' }};"
                                        title="{{ $set->is_active ? 'Disable' : 'Enable' }}">
                                    <i class="fa-solid {{ $set->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5" style="color: #627e71;">
                            <i class="fa-solid fa-layer-group fs-2 mb-3 d-block" style="color: #c2a889;"></i>
                            No consolidated sets configured yet.
                            <a href="{{ route('admin.facilities.create', ['type' => 'set']) }}" style="color: #334c42; font-weight: bold; text-decoration: underline;">Create a facility set</a> to group multiple spaces.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex align-items-center justify-content-between p-3" style="background-color: #fcfbf9; border-top: 1px solid #e2d3be;">
            <span class="text-muted small" style="color: #627e71 !important;">
                Showing <span class="badge" style="background-color: #f3ede4; color: #3d332a; border: 1px solid #e2d3be;">{{ $facilitySets->firstItem() ?? 0 }}-{{ $facilitySets->lastItem() ?? 0 }}</span> of <strong>{{ $facilitySets->total() }}</strong>
            </span>
            <div class="m-0">
                {{ $facilitySets->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
