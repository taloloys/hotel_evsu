@extends('layouts.app')

@section('title', 'Add Facility')
@section('pageTitle', 'Add Facility')
@section('pageSubtitle', 'Create a new rentable facility for reservation')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        <div class="mb-3">
            <a href="{{ route('frontdesk.facilities.index') }}"
               class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Facilities
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="border:1px solid #c2a889 !important;">
            <form action="{{ route('frontdesk.facilities.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-header bg-white py-3 px-4" style="border-bottom:1px solid #f0e8de;">
                    <h5 class="mb-0 fw-bold" style="color:#1a1a1a;">
                        <i class="fa-solid fa-building me-2" style="color:#334c42;"></i>New Facility Details
                    </h5>
                </div>

                <div class="card-body p-4 p-md-5">

                    @if($errors->any())
                    <div class="alert alert-danger rounded-3 border-0 shadow-sm">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- BASIC DETAILS --}}
                    <h6 class="fw-bold text-uppercase small mb-3 pb-2 border-bottom" style="color:#334c42;letter-spacing:.05em;">
                        <i class="fa-solid fa-info-circle me-1"></i> Basic Details
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Facility Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="e.g. Basketball Court, Function Hall, Conference Room"
                                   required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Capacity (Pax)</label>
                            <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror"
                                   value="{{ old('capacity') }}" min="1" placeholder="e.g. 50">
                            @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="3" placeholder="Describe what this facility is and what's included...">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- PRICING & STATUS --}}
                    <h6 class="fw-bold text-uppercase small mb-3 pb-2 border-bottom" style="color:#334c42;letter-spacing:.05em;">
                        <i class="fa-solid fa-tag me-1"></i> Pricing &amp; Status
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Rate Type <span class="text-danger">*</span></label>
                            <select name="rate_type" class="form-select @error('rate_type') is-invalid @enderror" required>
                                <option value="hourly" {{ old('rate_type', 'hourly') === 'hourly' ? 'selected' : '' }}>
                                    Hourly — charged per hour
                                </option>
                                <option value="daily" {{ old('rate_type') === 'daily' ? 'selected' : '' }}>
                                    Daily — flat rate per day
                                </option>
                            </select>
                            @error('rate_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Rate (₱) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" step="0.01" name="rate"
                                       class="form-control @error('rate') is-invalid @enderror"
                                       value="{{ old('rate') }}" required min="0"
                                       placeholder="e.g. 500">
                            </div>
                            @error('rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control"
                                   value="{{ old('sort_order', 0) }}" min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Active</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                       id="isActiveSwitch"
                                       {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label text-muted small" for="isActiveSwitch">
                                    Enabled
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- IMAGES --}}
                    <h6 class="fw-bold text-uppercase small mb-3 pb-2 border-bottom" style="color:#334c42;letter-spacing:.05em;">
                        <i class="fa-solid fa-images me-1"></i> Photos <span class="text-muted fw-normal">(optional)</span>
                    </h6>

                    <div class="mb-2">
                        <input type="file" name="images[]" class="form-control" multiple
                               accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Supported: JPEG, PNG, WEBP. Max 4 MB per image.</div>
                    </div>

                </div>

                <div class="card-footer bg-light p-4 d-flex justify-content-between align-items-center"
                     style="border-top:1px solid #f0e8de;">
                    <a href="{{ route('frontdesk.facilities.index') }}"
                       class="btn btn-outline-secondary rounded-pill">Cancel</a>
                    <button type="submit" class="btn rounded-pill px-4 fw-semibold shadow-sm text-white"
                            style="background:#334c42;border-color:#334c42;">
                        <i class="fa-solid fa-save me-1"></i> Save Facility
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
