@extends('layouts.app')

@section('title', 'Manage Facilities')
@section('pageTitle', 'Manage Facilities')
@section('pageSubtitle', 'Create, edit, and configure rentable facilities for public booking')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 text-dark fw-bold"><i class="fa-solid fa-building me-2"></i>Facilities</h5>
        <a href="{{ route('admin.facilities.create') }}" class="btn btn-primary btn-sm rounded-pill fw-semibold shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Add Facility
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th class="ps-4">Image</th>
                        <th>Name</th>
                        <th>Rate</th>
                        <th>Capacity</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($facilities as $facility)
                    <tr>
                        <td class="ps-4">
                            @if(!empty($facility->images) && count($facility->images) > 0)
                                <img src="{{ \App\Models\Facility::imageUrl($facility->images[0]) }}" class="rounded shadow-sm" style="width: 60px; height: 45px; object-fit: cover;" alt="{{ $facility->name }}">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 45px;">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td class="fw-semibold text-dark">{{ $facility->name }}</td>
                        <td>₱{{ number_format($facility->rate, 2) }} / <span class="badge bg-secondary">{{ ucfirst($facility->rate_type) }}</span></td>
                        <td>{{ $facility->capacity ? $facility->capacity . ' pax' : 'N/A' }}</td>
                        <td>{{ $facility->sort_order }}</td>
                        <td>
                            <form action="{{ route('admin.facilities.toggle', $facility) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm rounded-pill px-3 {{ $facility->is_active ? 'btn-success' : 'btn-outline-secondary' }}" style="font-size: 0.8rem;">
                                    {{ $facility->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.facilities.edit', $facility) }}" class="btn btn-sm btn-outline-primary rounded-circle" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('admin.facilities.destroy', $facility) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this facility? This will not delete past reservations, but the facility will no longer be visible.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fa-solid fa-folder-open fs-2 mb-3"></i>
                            <p class="mb-0">No facilities found. Click "Add Facility" to create one.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
