@extends('layouts.app')

@section('title', 'Add Facility')
@section('pageTitle', 'Add Facility')
@section('pageSubtitle', 'Create a new rentable facility')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        
        <div class="mb-3">
            <a href="{{ route('admin.facilities.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Facilities
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <form action="{{ route('admin.facilities.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body p-4 p-md-5">
                    
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <h5 class="text-primary fw-bold mb-4 border-bottom pb-2">Basic Details</h5>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Facility Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Capacity (Pax)</label>
                            <input type="number" name="capacity" class="form-control" value="{{ old('capacity') }}" min="1">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <h5 class="text-primary fw-bold mb-4 border-bottom pb-2">Pricing & Status</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Rate (₱) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="rate" class="form-control" value="{{ old('rate') }}" required min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Rate Type <span class="text-danger">*</span></label>
                            <select name="rate_type" class="form-select" required>
                                <option value="hourly" {{ old('rate_type') === 'hourly' ? 'selected' : '' }}>Hourly</option>
                                <option value="daily" {{ old('rate_type') === 'daily' ? 'selected' : '' }}>Daily (Flat Rate)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Active</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="transform: scale(1.3); margin-left: -1.5rem;">
                            </div>
                        </div>
                    </div>

                    <h5 class="text-primary fw-bold mb-4 border-bottom pb-2">Images</h5>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Upload Photos</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Supported formats: JPEG, PNG, WEBP. Max size: 4MB per image.</div>
                    </div>

                </div>
                <div class="card-footer bg-light p-4 text-end">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm rounded-pill">
                        <i class="fa-solid fa-save me-1"></i> Save Facility
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
