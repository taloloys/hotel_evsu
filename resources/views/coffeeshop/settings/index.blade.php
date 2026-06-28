@extends('layouts.app')

@section('title', 'POS Settings')
@section('pageTitle', 'POS Settings')
@section('pageSubtitle', 'Configure inventory alerts and product categories')

@section('content')
@include('coffeeshop.partials.alerts')

<style>
    /* ===========================
   APP TABS (GLOBAL STYLE)
=========================== */

.app-tabs .nav-link{
    background:#e9ecef;
    color:#495057;
    border:1px solid #dee2e6;
    font-weight:600;
    border-radius:999px;
    padding:.65rem 1rem;
    box-shadow:0 .125rem .25rem rgba(0,0,0,.05);
    transition:all .2s ease;
}

.app-tabs .nav-link:hover{
    background:#dee2e6;
    color:#212529;
    border-color:#ced4da;
}

.app-tabs .nav-link.active{
    background:#0d6efd;
    color:#fff;
    border-color:#0d6efd;
    box-shadow:0 .25rem .5rem rgba(13,110,253,.25);
}

/* icon spacing consistency */
.app-tabs .nav-link i{
    font-size:.9rem;
}
</style>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">

    {{-- ================= HEADER ================= --}}
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div class="fw-semibold">Settings</div>
        <small class="text-muted">Inventory • Categories • Configuration</small>
    </div>

    {{-- ================= TAB NAV ================= --}}
    <div class="px-3 pt-3 bg-white border-bottom">

        <ul class="nav nav-pills nav-fill gap-2 fw-semibold app-tabs" id="settingsTabs" role="tablist">

            <li class="nav-item">
                <button class="nav-link active rounded-pill"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-inventory"
                        type="button">
                    <i class="fa-solid fa-boxes-stacked me-2"></i>
                    Inventory Settings
                </button>
            </li>

            <li class="nav-item">
                <button class="nav-link rounded-pill"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-add-category"
                        type="button">
                    <i class="fa-solid fa-plus me-2"></i>
                    Add Category
                </button>
            </li>

            <li class="nav-item">
                <button class="nav-link rounded-pill"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-categories"
                        type="button">
                    <i class="fa-solid fa-list me-2"></i>
                    Categories
                </button>
            </li>

        </ul>

    </div>

    <hr class="my-0">

    {{-- ================= TAB CONTENT ================= --}}
    <div class="card-body">

        <div class="tab-content">

            {{-- ================= INVENTORY SETTINGS ================= --}}
            <div class="tab-pane fade show active" id="tab-inventory">

                <form action="{{ route('coffeeshop.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <label class="form-label">Default Low Stock Threshold</label>

                    <input type="number"
                           min="1"
                           name="default_low_stock_threshold"
                           class="form-control mb-2"
                           value="{{ $defaultLowStockThreshold }}"
                           required>

                    <small class="text-muted d-block mb-3">
                        Used when a product has no custom threshold.
                        Walk-in folio ID: {{ $walkInFolioId ?? 'Not configured' }}
                    </small>

                    <button class="btn btn-primary">
                        Save Settings
                    </button>
                </form>

            </div>

            {{-- ================= ADD CATEGORY ================= --}}
            <div class="tab-pane fade" id="tab-add-category">

                <form action="{{ route('coffeeshop.settings.categories.store') }}" method="POST">
                    @csrf

                    <label class="form-label">Category Name</label>
                    <input type="text" name="name" class="form-control mb-2" required>

                    <label class="form-label">Sort Order</label>
                    <input type="number" min="0" name="sort_order" class="form-control mb-3" value="0">

                    <button class="btn btn-outline-primary">
                        Add Category
                    </button>
                </form>

            </div>

            {{-- ================= CATEGORIES TABLE ================= --}}
            <div class="tab-pane fade" id="tab-categories">

                <div class="table-responsive">

                    <table class="table table-sm align-middle mb-0">

                        <thead class="table-light text-muted small">
                            <tr>
                                <th>Name</th>
                                <th>Sort</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($categories as $category)
                                <tr>
                                    <td class="fw-semibold">{{ $category->name }}</td>
                                    <td>{{ $category->sort_order }}</td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">

                                        <form action="{{ route('coffeeshop.settings.categories.toggle', $category) }}"
                                              method="POST">
                                            @csrf
                                            @method('PATCH')

                                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                {{ $category->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">
                                        No categories found.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
</div>
@endsection
