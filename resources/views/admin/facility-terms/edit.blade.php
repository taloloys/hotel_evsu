@extends('layouts.app')

@section('title', 'Facility Terms & Conditions')
@section('pageTitle', 'Facility Terms & Conditions')
@section('pageSubtitle', 'Manage the booking terms presented to users')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark fw-bold"><i class="fa-solid fa-file-contract me-2"></i>Edit Terms & Conditions</h5>
                @if($updatedAt)
                    <span class="badge bg-light text-muted border"><i class="fa-regular fa-clock me-1"></i> Last updated: {{ \Carbon\Carbon::parse($updatedAt)->format('M d, Y h:i A') }}</span>
                @endif
            </div>
            
            <form action="{{ route('admin.facility-terms.update') }}" method="POST">
                @csrf
                @method('PUT')
                
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

                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Content</label>
                        <textarea name="content" class="form-control font-monospace text-muted" rows="15" placeholder="Enter terms and conditions here (plain text, line breaks will be preserved)..." required>{{ old('content', $content) }}</textarea>
                        <div class="form-text mt-2"><i class="fa-solid fa-circle-info me-1"></i> Use plain text. Newlines will be automatically converted to line breaks on the public page. HTML is not supported for security reasons.</div>
                    </div>

                </div>
                
                <div class="card-footer bg-light p-4 text-end">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm rounded-pill">
                        <i class="fa-solid fa-save me-1"></i> Save Terms
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
