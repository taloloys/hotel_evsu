@extends('layouts.app')

@section('title', $isSet ? 'Edit Facility Set' : 'Edit Facility')
@section('pageTitle', $isSet ? 'Edit Facility Set' : 'Edit Facility')
@section('pageSubtitle', $isSet ? ('Editing consolidated set: ' . $facilitySet->name) : ('Editing facility: ' . $facility->name))

@php
    $subject   = $isSet ? $facilitySet : $facility;
    $editName  = $isSet ? $facilitySet->name : $facility->name;
    $memberIds = $isSet ? $facilitySet->facilities->pluck('facility_id')->toArray() : [];
@endphp

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        <div class="mb-3">
            <a href="{{ route('admin.facilities.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Facilities
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <form action="{{ route('admin.facilities.update', $subject->getKey()) }}{{ $isSet ? '?type=set' : '' }}"
                  method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                {{-- Locked facility_type hidden field --}}
                <input type="hidden" name="facility_type" value="{{ $isSet ? 'set' : 'single' }}">

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

                    {{-- TYPE BADGE --}}
                    <div class="d-flex align-items-center gap-2 mb-4">
                        @if($isSet)
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6">
                                <i class="fa-solid fa-layer-group me-1"></i> Consolidated Facility Set
                            </span>
                        @else
                            <span class="badge bg-primary px-3 py-2 rounded-pill fs-6">
                                <i class="fa-solid fa-building me-1"></i> Individual Facility
                            </span>
                        @endif
                    </div>

                    {{-- BASIC DETAILS -------------------------------------------------------}}
                    <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">Basic Details</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">{{ $isSet ? 'Set Name' : 'Facility Name' }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ old('name', $subject->name) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Capacity (Pax)</label>
                            <input type="number" name="capacity" class="form-control"
                                   value="{{ old('capacity', $subject->capacity) }}" min="1">
                            @if($isSet)
                                <div class="form-text">Leave blank to auto-sum member capacities.</div>
                            @endif
                        </div>
                        @if($isSet)
                        <div class="col-12">
                            <label class="form-label fw-bold">Reservation Prefix Code <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="text" name="prefix_code" class="form-control"
                                   value="{{ old('prefix_code', $facilitySet->prefix_code) }}" maxlength="20"
                                   placeholder="e.g. EVSUOCFH">
                            <div class="form-text">Used in reference numbers like <code>#EVSUOCFH-20261001-001</code>. Auto-generated from name if blank.</div>
                        </div>
                        @endif
                        <div class="col-12">
                            <label class="form-label fw-bold">Description <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $subject->description) }}</textarea>
                        </div>
                    </div>

                    {{-- MEMBER FACILITIES (sets only) ---------------------------------------}}
                    @if($isSet)
                    <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">Member Facilities</h5>
                    <div class="alert alert-info border-0 rounded-3 small py-2">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Reserving this set blocks <strong>all selected spaces</strong> simultaneously.
                    </div>
                    <div class="row g-2 mt-2 mb-4">
                        @foreach($individualFacilities as $f)
                        <div class="col-md-4 col-6">
                            <label class="d-flex align-items-center gap-2 border rounded-3 p-2 cursor-pointer" style="font-size:.9rem;">
                                <input type="checkbox" name="member_facilities[]"
                                       value="{{ $f->facility_id }}"
                                       class="form-check-input mt-0 flex-shrink-0"
                                       {{ in_array($f->facility_id, old('member_facilities', $memberIds)) ? 'checked' : '' }}>
                                <span>
                                    <span class="fw-semibold">{{ $f->name }}</span>
                                    @if($f->capacity)
                                        <span class="text-muted" style="font-size:.78rem;"> · {{ $f->capacity }} pax</span>
                                    @endif
                                </span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- PRICING & STATUS ---------------------------------------------------}}
                    <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">Pricing & Status</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Rate Mode <span class="text-danger">*</span></label>
                            <select name="rate_type" class="form-select" required>
                                @php
                                    $currentRateType = old('rate_type', $subject->rate_type ?? 'hourly');
                                    $hasHourly = $subject->hourly_rate !== null;
                                    $hasDaily  = $subject->daily_rate  !== null;
                                    if ($hasHourly && $hasDaily) $currentRateType = old('rate_type', 'both');
                                @endphp
                                <option value="hourly" {{ $currentRateType === 'hourly' ? 'selected' : '' }}>Hourly only</option>
                                <option value="daily"  {{ $currentRateType === 'daily'  ? 'selected' : '' }}>Daily only</option>
                                <option value="both"   {{ $currentRateType === 'both'   ? 'selected' : '' }}>Both (hourly & daily)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Hourly Rate (₱)</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" step="0.01" name="hourly_rate" class="form-control"
                                       value="{{ old('hourly_rate', $subject->hourly_rate) }}" min="0">
                                <span class="input-group-text text-muted">/hr</span>
                            </div>
                            @if($isSet)
                                <div class="form-text">Blank = auto-sum of member hourly rates.</div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Daily Rate (₱)</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" step="0.01" name="daily_rate" class="form-control"
                                       value="{{ old('daily_rate', $subject->daily_rate) }}" min="0">
                                <span class="input-group-text text-muted">/day</span>
                            </div>
                            @if($isSet)
                                <div class="form-text">Blank = auto-sum of member daily rates.</div>
                            @endif
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div>
                                <label class="form-label fw-bold d-block">Status</label>
                                <div class="form-check form-switch mt-1">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                           id="isActiveSwitchEdit"
                                           {{ old('is_active', $subject->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isActiveSwitchEdit">Active (publicly visible)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- IMAGES -------------------------------------------------------------}}
                    <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">Images <span class="text-muted fw-normal fs-6">(optional)</span></h5>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Upload Additional Photos</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Supported formats: JPEG, PNG, WEBP. Max 4 MB per image.</div>
                    </div>

                    {{-- Existing images (Alpine-powered removal) --}}
                    <div x-data="{
                        images: [
                            @foreach($subject->images ?? [] as $img)
                                { path: '{{ $img }}', url: '{{ \App\Models\Facility::imageUrl($img) }}' }@if(!$loop->last),@endif
                            @endforeach
                        ],
                        removeImage(index) {
                            this.images.splice(index, 1);
                        }
                    }">
                        <input type="hidden" name="image_paths" :value="images.map(i => i.path).join(',')">

                        <div class="row g-3" x-show="images.length > 0">
                            <template x-for="(img, index) in images" :key="index">
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="position-relative bg-light rounded shadow-sm border overflow-hidden">
                                        <img :src="img.url" class="w-100 object-fit-cover" style="height:120px;">
                                        <button type="button" @click.prevent="removeImage(index)"
                                                class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle p-1 shadow"
                                                style="width:28px;height:28px;line-height:1;">
                                            <i class="fa-solid fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div x-show="images.length === 0" class="text-muted fst-italic mt-2">
                            <i class="fa-solid fa-image text-muted me-1"></i> No existing images. Upload above.
                        </div>
                    </div>

                </div>

                <div class="card-footer bg-light p-4 text-end">
                    <a href="{{ route('admin.facilities.index') }}" class="btn btn-outline-secondary rounded-pill me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm rounded-pill">
                        <i class="fa-solid fa-save me-1"></i> Update {{ $isSet ? 'Facility Set' : 'Facility' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
