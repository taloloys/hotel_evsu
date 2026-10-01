@extends('layouts.app')

@section('title', 'Manage Facilities')
@section('pageTitle', 'Facilities')
@section('pageSubtitle', 'Manage rentable facilities — courts, halls, conference rooms, and more')

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
        <div class="card border-0 shadow-sm rounded-4" style="background:#ffffff; border:1px solid #c2a889 !important;">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:52px;height:52px;background:#e8f0ec;color:#334c42;">
                    <i class="fa-solid fa-building fs-4"></i>
                </div>
                <div>
                    <div class="small fw-bold" style="color:#1a1a1a;">Total Facilities</div>
                    <h3 class="fw-bold mb-0 mt-1" style="color:#1a1a1a;font-size:1.85rem;">{{ $facilities->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4" style="background:#ffffff; border:1px solid #c2a889 !important;">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:52px;height:52px;background:#e8f0ec;color:#334c42;">
                    <i class="fa-solid fa-toggle-on fs-4"></i>
                </div>
                <div>
                    <div class="small fw-bold" style="color:#1a1a1a;">Active Facilities</div>
                    <h3 class="fw-bold mb-0 mt-1" style="color:#1a1a1a;font-size:1.85rem;">{{ $facilities->where('is_active', true)->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <a href="{{ route('frontdesk.facility-reservations.index', ['status' => 'pending']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4" style="background:#ffffff; border:1px solid #c2a889 !important;">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:52px;height:52px;background:#fff3cd;color:#92400e;">
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

{{-- FACILITIES TABLE --}}
<div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="border:1px solid #c2a889 !important;">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 px-4" style="border-bottom:1px solid #f0e8de;">
        <h5 class="mb-0 fw-bold" style="color:#1a1a1a;">
            <i class="fa-solid fa-building me-2" style="color:#334c42;"></i>All Facilities
        </h5>
        <div class="d-flex gap-2">
            <a href="{{ route('frontdesk.facility-reservations.index') }}"
               class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm fw-semibold">
                <i class="fa-solid fa-calendar-check me-1"></i> View Reservations
            </a>
            <a href="{{ route('frontdesk.facilities.create') }}"
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
                        <th>Rate</th>
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
                                <div class="small text-muted text-truncate" style="max-width:250px;">{{ $facility->description }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold text-success">₱{{ number_format($facility->rate, 2) }}</span>
                            <span class="badge ms-1 rounded-pill px-2"
                                  style="background:{{ $facility->rate_type === 'hourly' ? '#dbeafe' : '#f3e8ff' }};
                                         color:{{ $facility->rate_type === 'hourly' ? '#1e40af' : '#7c3aed' }};
                                         font-size:0.72rem;font-weight:600;">
                                {{ ucfirst($facility->rate_type) }}
                            </span>
                        </td>
                        <td class="text-muted small">
                            {{ $facility->capacity ? $facility->capacity . ' pax' : 'N/A' }}
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3"
                                  style="background:#e8f0ec;color:#334c42;font-weight:600;">
                                {{ $facility->reservations_count }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('frontdesk.facilities.toggle', $facility) }}"
                                  method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="btn btn-sm rounded-pill px-3 fw-semibold {{ $facility->is_active ? 'btn-success' : 'btn-outline-secondary' }}"
                                        style="font-size:0.8rem;">
                                    {{ $facility->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('frontdesk.facilities.edit', $facility) }}"
                               class="btn btn-sm btn-outline-primary rounded-circle me-1" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('frontdesk.facilities.destroy', $facility) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this facility? Past reservations will be kept but the facility will no longer be bookable.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-3">
                                <i class="fa-solid fa-building fs-1 mb-3 d-block" style="color:#c2a889;"></i>
                                <p class="fw-semibold mb-1" style="color:#1a1a1a;">No facilities yet</p>
                                <p class="text-muted small mb-3">Add facilities like basketball courts, function halls, or conference rooms.</p>
                                <a href="{{ route('frontdesk.facilities.create') }}"
                                   class="btn btn-sm rounded-pill px-4 fw-semibold text-white shadow-sm"
                                   style="background:#334c42;">
                                    <i class="fa-solid fa-plus me-1"></i> Add First Facility
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
