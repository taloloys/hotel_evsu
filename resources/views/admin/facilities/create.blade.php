@extends('layouts.app')

@section('title', 'Add Facility')
@section('pageTitle', 'Add Facility')
@section('pageSubtitle', 'Create a new individual space or a consolidated facility set')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        <div class="mb-3">
            <a href="{{ route('admin.facilities.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Facilities
            </a>
        </div>

        <div class="card shadow-sm border-0" x-data="facilityForm()">
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

                    {{-- FACILITY TYPE --------------------------------------------------------}}
                    <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">Facility Type</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="d-block cursor-pointer" @click="type = 'single'">
                                <div :class="type === 'single'
                                    ? 'border-primary bg-primary bg-opacity-10'
                                    : 'border-secondary-subtle bg-light'"
                                     class="border rounded-3 p-3 d-flex align-items-start gap-3 transition">
                                    <i class="fa-solid fa-building fa-lg mt-1 text-primary"></i>
                                    <div>
                                        <div class="fw-bold">Single Facility</div>
                                        <div class="small text-muted">An individual bookable space — e.g. Basketball Court, Conference Room.</div>
                                    </div>
                                </div>
                                <input type="radio" name="facility_type" value="single" class="d-none"
                                       :checked="type === 'single'" x-model="type"
                                       {{ old('facility_type', request('type', 'single')) === 'single' ? 'checked' : '' }}>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="d-block cursor-pointer" @click="type = 'set'">
                                <div :class="type === 'set'
                                    ? 'border-warning bg-warning bg-opacity-10'
                                    : 'border-secondary-subtle bg-light'"
                                     class="border rounded-3 p-3 d-flex align-items-start gap-3 transition">
                                    <i class="fa-solid fa-layer-group fa-lg mt-1 text-warning"></i>
                                    <div>
                                        <div class="fw-bold">Consolidated Facility Set</div>
                                        <div class="small text-muted">A package grouping multiple spaces into one bookable unit — e.g. Function Hall (A+B).</div>
                                    </div>
                                </div>
                                <input type="radio" name="facility_type" value="set" class="d-none"
                                       :checked="type === 'set'" x-model="type"
                                       {{ old('facility_type', request('type', 'single')) === 'set' ? 'checked' : '' }}>
                            </label>
                        </div>
                    </div>

                    {{-- BASIC DETAILS -------------------------------------------------------}}
                    <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">Basic Details</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">
                                <span x-text="type === 'set' ? 'Set Name' : 'Facility Name'"></span>
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                   :placeholder="type === 'set' ? 'e.g. Function Hall, Grand Venue' : 'e.g. Basketball Court, Conference Room A'"
                                   required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Capacity (Pax)</label>
                            <input type="number" name="capacity" class="form-control"
                                   value="{{ old('capacity') }}" min="1"
                                   :placeholder="type === 'set' ? 'Combined capacity (optional)' : 'e.g. 50'">
                            <div class="form-text" x-show="type === 'set'">Leave blank to auto-sum member capacities.</div>
                        </div>
                        <div class="col-12" x-show="type === 'set'" x-cloak x-transition>
                            <label class="form-label fw-bold">Reservation Prefix Code <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="text" name="prefix_code" class="form-control"
                                   value="{{ old('prefix_code') }}" maxlength="20"
                                   placeholder="e.g. EVSUOCFH — used in reference numbers like #EVSUOCFH-20261001-001">
                            <div class="form-text">If left blank, initials are auto-generated from the set name.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Description <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="Describe the facility or what's included...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    {{-- MEMBER FACILITIES (sets only) ---------------------------------------}}
                    <div x-show="type === 'set'" x-cloak x-transition class="mb-4">
                        <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">Member Facilities</h5>
                        <div class="alert alert-info border-0 rounded-3 small py-2">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            Reserving this set will simultaneously block <strong>all selected individual facilities</strong> below.
                        </div>
                        <div class="row g-2 mt-2">
                            @forelse($individualFacilities as $f)
                            <div class="col-md-4 col-6">
                                <label class="d-flex align-items-center gap-2 border rounded-3 p-2 cursor-pointer hover-bg-light" style="font-size:.9rem;">
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
                    <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">Pricing & Status</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Rate Mode <span class="text-danger">*</span></label>
                            <select name="rate_type" class="form-select" required>
                                <option value="hourly" {{ old('rate_type', 'hourly') === 'hourly' ? 'selected' : '' }}>Hourly only</option>
                                <option value="daily" {{ old('rate_type') === 'daily' ? 'selected' : '' }}>Daily only</option>
                                <option value="both" {{ old('rate_type') === 'both' ? 'selected' : '' }}>Both (hourly & daily)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Hourly Rate (₱) <span class="text-muted fw-normal" x-show="type !== 'set'">(per hr)</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" step="0.01" name="hourly_rate" class="form-control"
                                       value="{{ old('hourly_rate') }}" min="0" placeholder="e.g. 800.00">
                                <span class="input-group-text text-muted">/hr</span>
                            </div>
                            <div class="form-text" x-show="type === 'set'">Leave blank to auto-sum member hourly rates.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Daily Rate (₱) <span class="text-muted fw-normal">(flat/day)</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" step="0.01" name="daily_rate" class="form-control"
                                       value="{{ old('daily_rate') }}" min="0" placeholder="e.g. 8000.00">
                                <span class="input-group-text text-muted">/day</span>
                            </div>
                            <div class="form-text" x-show="type === 'set'">Leave blank to auto-sum member daily rates.</div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div>
                                <label class="form-label fw-bold d-block">Status</label>
                                <div class="form-check form-switch mt-1">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                           id="isActiveSwitch" {{ old('is_active', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isActiveSwitch">Active (publicly visible)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- IMAGES -------------------------------------------------------------}}
                    <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">Images <span class="text-muted fw-normal fs-6">(optional)</span></h5>
                    <div class="mb-2">
                        <input type="file" name="images[]" class="form-control" multiple
                               accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Supported formats: JPEG, PNG, WEBP. Max 4 MB per image.</div>
                    </div>

                </div>

                <div class="card-footer bg-light p-4 text-end">
                    <a href="{{ route('admin.facilities.index') }}" class="btn btn-outline-secondary rounded-pill me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm rounded-pill">
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
