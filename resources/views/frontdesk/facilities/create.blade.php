@extends('layouts.app')

@section('title', 'Add Facility')
@section('pageTitle', 'Add Facility')
@section('pageSubtitle', 'Create a new individual space or a consolidated facility set')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        <div class="mb-3">
            <a href="{{ route('frontdesk.facilities.index') }}"
               class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Facilities
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="border:1px solid #c2a889 !important;"
             x-data="facilityForm()">
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

                    {{-- FACILITY TYPE --------------------------------------------------------}}
                    <h6 class="fw-bold text-uppercase small mb-3 pb-2 border-bottom" style="color:#334c42;letter-spacing:.05em;">
                        <i class="fa-solid fa-sliders me-1"></i> Facility Type
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="d-block cursor-pointer" @click="type = 'single'">
                                <div :class="type === 'single' ? 'border-primary bg-primary bg-opacity-10' : 'border-secondary-subtle bg-light'"
                                     class="border rounded-3 p-3 d-flex align-items-start gap-3">
                                    <i class="fa-solid fa-building fa-lg mt-1 text-primary"></i>
                                    <div>
                                        <div class="fw-bold">Single Facility</div>
                                        <div class="small text-muted">An individual bookable space — e.g. Basketball Court, Conference Room A.</div>
                                    </div>
                                </div>
                                <input type="radio" name="facility_type" value="single" class="d-none" x-model="type"
                                       {{ old('facility_type', request('type', 'single')) === 'single' ? 'checked' : '' }}>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="d-block cursor-pointer" @click="type = 'set'">
                                <div :class="type === 'set' ? 'border-warning bg-warning bg-opacity-10' : 'border-secondary-subtle bg-light'"
                                     class="border rounded-3 p-3 d-flex align-items-start gap-3">
                                    <i class="fa-solid fa-layer-group fa-lg mt-1 text-warning"></i>
                                    <div>
                                        <div class="fw-bold">Consolidated Facility Set</div>
                                        <div class="small text-muted">Groups multiple spaces into one unit — e.g. Function Hall (A + B).</div>
                                    </div>
                                </div>
                                <input type="radio" name="facility_type" value="set" class="d-none" x-model="type"
                                       {{ old('facility_type', request('type', 'single')) === 'set' ? 'checked' : '' }}>
                            </label>
                        </div>
                    </div>

                    {{-- BASIC DETAILS -------------------------------------------------------}}
                    <h6 class="fw-bold text-uppercase small mb-3 pb-2 border-bottom" style="color:#334c42;letter-spacing:.05em;">
                        <i class="fa-solid fa-info-circle me-1"></i> Basic Details
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">
                                <span x-text="type === 'set' ? 'Set Name' : 'Facility Name'"></span>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   :placeholder="type === 'set' ? 'e.g. Function Hall, Grand Venue' : 'e.g. Basketball Court, Conference Room'"
                                   required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Capacity (Pax)</label>
                            <input type="number" name="capacity"
                                   class="form-control @error('capacity') is-invalid @enderror"
                                   value="{{ old('capacity') }}" min="1"
                                   :placeholder="type === 'set' ? 'Combined (optional)' : 'e.g. 50'">
                            <div class="form-text" x-show="type === 'set'">Leave blank to auto-sum member capacities.</div>
                            @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12" x-show="type === 'set'" x-transition>
                            <label class="form-label fw-semibold">Reservation Prefix Code <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="text" name="prefix_code"
                                   class="form-control @error('prefix_code') is-invalid @enderror"
                                   value="{{ old('prefix_code') }}" maxlength="20"
                                   placeholder="e.g. EVSUOCFH — used in ref numbers like #EVSUOCFH-20261001-001">
                            <div class="form-text">Auto-generated from name if left blank.</div>
                            @error('prefix_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="3" placeholder="Describe the facility or what's included...">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- MEMBER FACILITIES (sets only) ---------------------------------------}}
                    <div x-show="type === 'set'" x-cloak x-transition class="mb-4">
                        <h6 class="fw-bold text-uppercase small mb-3 pb-2 border-bottom" style="color:#334c42;letter-spacing:.05em;">
                            <i class="fa-solid fa-list-check me-1"></i> Member Facilities
                        </h6>
                        <div class="alert alert-info border-0 rounded-3 small py-2">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            All selected spaces below will be <strong>blocked simultaneously</strong> when this set is reserved.
                        </div>
                        <div class="row g-2 mt-2">
                            @forelse($individualFacilities as $f)
                            <div class="col-md-4 col-6">
                                <label class="d-flex align-items-center gap-2 border rounded-3 p-2 cursor-pointer" style="font-size:.9rem;">
                                    <input type="checkbox" name="member_facilities[]"
                                           value="{{ $f->facility_id }}"
                                           class="form-check-input mt-0 flex-shrink-0"
                                           {{ in_array($f->facility_id, old('member_facilities', [])) ? 'checked' : '' }}>
                                    <span>
                                        <span class="fw-semibold">{{ $f->name }}</span>
                                        @if($f->capacity)
                                            <span class="text-muted" style="font-size:.78rem;"> · {{ $f->capacity }} pax</span>
                                        @endif
                                    </span>
                                </label>
                            </div>
                            @empty
                            <div class="col-12 text-muted fst-italic py-2">
                                <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i>
                                No individual facilities available yet. Please create single facilities first before creating a consolidated set.
                            </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- PRICING & STATUS ---------------------------------------------------}}
                    <h6 class="fw-bold text-uppercase small mb-3 pb-2 border-bottom" style="color:#334c42;letter-spacing:.05em;">
                        <i class="fa-solid fa-tag me-1"></i> Pricing &amp; Status
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Rate Mode <span class="text-danger">*</span></label>
                            <select name="rate_type" class="form-select @error('rate_type') is-invalid @enderror" required>
                                <option value="hourly" {{ old('rate_type', 'hourly') === 'hourly' ? 'selected' : '' }}>Hourly only</option>
                                <option value="daily"  {{ old('rate_type') === 'daily'  ? 'selected' : '' }}>Daily only</option>
                                <option value="both"   {{ old('rate_type') === 'both'   ? 'selected' : '' }}>Both (hourly & daily)</option>
                            </select>
                            @error('rate_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Hourly Rate (₱)</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" step="0.01" name="hourly_rate"
                                       class="form-control @error('hourly_rate') is-invalid @enderror"
                                       value="{{ old('hourly_rate') }}" min="0" placeholder="e.g. 800.00">
                                <span class="input-group-text text-muted">/hr</span>
                            </div>
                            <div class="form-text" x-show="type === 'set'">Leave blank to auto-sum member hourly rates.</div>
                            @error('hourly_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Daily Rate (₱)</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" step="0.01" name="daily_rate"
                                       class="form-control @error('daily_rate') is-invalid @enderror"
                                       value="{{ old('daily_rate') }}" min="0" placeholder="e.g. 8000.00">
                                <span class="input-group-text text-muted">/day</span>
                            </div>
                            <div class="form-text" x-show="type === 'set'">Leave blank to auto-sum member daily rates.</div>
                            @error('daily_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div>
                                <label class="form-label fw-semibold d-block">Status</label>
                                <div class="form-check form-switch mt-1">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                           id="isActiveSwitch"
                                           {{ old('is_active', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label text-muted small" for="isActiveSwitch">
                                        Active (publicly visible)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- IMAGES -------------------------------------------------------------}}
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
                        <i class="fa-solid fa-save me-1"></i>
                        <span x-text="type === 'set' ? 'Save Facility Set' : 'Save Facility'">Save Facility</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function facilityForm() {
    return {
        type: '{{ old('facility_type', request('type', 'single')) }}',
    }
}
</script>
@endsection
