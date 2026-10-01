@extends('layouts.app')

@section('title', $isSet ? 'Edit Facility Set' : 'Edit Facility')
@section('pageTitle', $isSet ? 'Edit Facility Set' : 'Edit Facility')
@section('pageSubtitle', $isSet ? ('Editing set: ' . $facilitySet->name) : ('Editing: ' . $facility->name))

@php
    $subject   = $isSet ? $facilitySet : $facility;
    $memberIds = $isSet ? $facilitySet->facilities->pluck('facility_id')->toArray() : [];
@endphp

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
            <form action="{{ route('frontdesk.facilities.update', $subject->getKey()) }}{{ $isSet ? '?type=set' : '' }}"
                  method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <input type="hidden" name="facility_type" value="{{ $isSet ? 'set' : 'single' }}">

                <div class="card-header bg-white py-3 px-4" style="border-bottom:1px solid #f0e8de;">
                    <div class="d-flex align-items-center gap-3">
                        @if($isSet)
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                <i class="fa-solid fa-layer-group me-1"></i> Consolidated Set
                            </span>
                        @else
                            <span class="badge px-3 py-2 rounded-pill text-white" style="background:#334c42;">
                                <i class="fa-solid fa-building me-1"></i> Individual Facility
                            </span>
                        @endif
                        <h5 class="mb-0 fw-bold" style="color:#1a1a1a;">
                            <i class="fa-solid fa-pen me-2" style="color:#334c42;"></i>Editing: {{ $subject->name }}
                        </h5>
                    </div>
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

                    {{-- BASIC DETAILS -------------------------------------------------------}}
                    <h6 class="fw-bold text-uppercase small mb-3 pb-2 border-bottom" style="color:#334c42;letter-spacing:.05em;">
                        <i class="fa-solid fa-info-circle me-1"></i> Basic Details
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">
                                {{ $isSet ? 'Set Name' : 'Facility Name' }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $subject->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Capacity (Pax)</label>
                            <input type="number" name="capacity"
                                   class="form-control @error('capacity') is-invalid @enderror"
                                   value="{{ old('capacity', $subject->capacity) }}" min="1">
                            @if($isSet)
                                <div class="form-text">Leave blank to auto-sum member capacities.</div>
                            @endif
                            @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        @if($isSet)
                        <div class="col-12">
                            <label class="form-label fw-semibold">Reservation Prefix Code <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="text" name="prefix_code"
                                   class="form-control @error('prefix_code') is-invalid @enderror"
                                   value="{{ old('prefix_code', $facilitySet->prefix_code) }}" maxlength="20"
                                   placeholder="e.g. EVSUOCFH">
                            <div class="form-text">Used in reference numbers like <code>#EVSUOCFH-20261001-001</code>. Auto-generated from name if blank.</div>
                            @error('prefix_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        @endif

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="3" placeholder="Describe the facility or what's included...">{{ old('description', $subject->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- MEMBER FACILITIES (sets only) ---------------------------------------}}
                    @if($isSet)
                    <h6 class="fw-bold text-uppercase small mb-3 pb-2 border-bottom" style="color:#334c42;letter-spacing:.05em;">
                        <i class="fa-solid fa-list-check me-1"></i> Member Facilities
                    </h6>
                    <div class="alert alert-info border-0 rounded-3 small py-2">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        All selected spaces below will be <strong>blocked simultaneously</strong> when this set is reserved.
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
                    <h6 class="fw-bold text-uppercase small mb-3 pb-2 border-bottom" style="color:#334c42;letter-spacing:.05em;">
                        <i class="fa-solid fa-tag me-1"></i> Pricing &amp; Status
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Rate Mode <span class="text-danger">*</span></label>
                            @php
                                $currentRateType = old('rate_type', $subject->rate_type ?? 'hourly');
                                $hasHourly = $subject->hourly_rate !== null;
                                $hasDaily  = $subject->daily_rate  !== null;
                                if ($hasHourly && $hasDaily) $currentRateType = old('rate_type', 'both');
                            @endphp
                            <select name="rate_type" class="form-select @error('rate_type') is-invalid @enderror" required>
                                <option value="hourly" {{ $currentRateType === 'hourly' ? 'selected' : '' }}>Hourly only</option>
                                <option value="daily"  {{ $currentRateType === 'daily'  ? 'selected' : '' }}>Daily only</option>
                                <option value="both"   {{ $currentRateType === 'both'   ? 'selected' : '' }}>Both (hourly & daily)</option>
                            </select>
                            @error('rate_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Hourly Rate (₱)</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" step="0.01" name="hourly_rate"
                                       class="form-control @error('hourly_rate') is-invalid @enderror"
                                       value="{{ old('hourly_rate', $subject->hourly_rate) }}" min="0">
                                <span class="input-group-text text-muted">/hr</span>
                            </div>
                            @if($isSet)<div class="form-text">Blank = auto-sum of member hourly rates.</div>@endif
                            @error('hourly_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Daily Rate (₱)</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" step="0.01" name="daily_rate"
                                       class="form-control @error('daily_rate') is-invalid @enderror"
                                       value="{{ old('daily_rate', $subject->daily_rate) }}" min="0">
                                <span class="input-group-text text-muted">/day</span>
                            </div>
                            @if($isSet)<div class="form-text">Blank = auto-sum of member daily rates.</div>@endif
                            @error('daily_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div>
                                <label class="form-label fw-semibold d-block">Status</label>
                                <div class="form-check form-switch mt-1">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                           id="isActiveSwitchEdit"
                                           {{ old('is_active', $subject->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label text-muted small" for="isActiveSwitchEdit">
                                        Active (publicly visible)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- IMAGES -------------------------------------------------------------}}
                    <h6 class="fw-bold text-uppercase small mb-3 pb-2 border-bottom" style="color:#334c42;letter-spacing:.05em;">
                        <i class="fa-solid fa-images me-1"></i> Photos
                    </h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Upload Additional Photos</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Supported: JPEG, PNG, WEBP. Max 4 MB per image.</div>
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
                                    <div class="position-relative bg-light rounded-3 shadow-sm overflow-hidden">
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

                        <div x-show="images.length === 0" class="text-muted fst-italic small mt-2">
                            <i class="fa-solid fa-image me-1"></i> No existing images. Upload above to add photos.
                        </div>
                    </div>

                </div>

                <div class="card-footer bg-light p-4 d-flex justify-content-between align-items-center"
                     style="border-top:1px solid #f0e8de;">
                    <a href="{{ route('frontdesk.facilities.index') }}"
                       class="btn btn-outline-secondary rounded-pill">Cancel</a>
                    <button type="submit" class="btn rounded-pill px-4 fw-semibold shadow-sm text-white"
                            style="background:#334c42;border-color:#334c42;">
                        <i class="fa-solid fa-save me-1"></i> Update {{ $isSet ? 'Facility Set' : 'Facility' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
