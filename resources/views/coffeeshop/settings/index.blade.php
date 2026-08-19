@extends('layouts.app')

@section('title', 'POS Settings')
@section('pageTitle', 'POS Settings')
@section('pageSubtitle', 'Configure inventory alerts and product categories')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="coffeeshop-page-shell">
    <div class="coffeeshop-hero">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
            <div>
                <div class="fw-bold fs-4" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">POS settings and categories</div>
                <div class="opacity-75 mt-1" style="font-size: 0.95rem;">Fine-tune inventory thresholds and keep your menu organization tidy.</div>
            </div>
        </div>
    </div>

    <div class="coffeeshop-panel overflow-hidden">

    {{-- ================= HEADER ================= --}}
    <div class="card-header bg-white d-flex justify-content-between align-items-center p-3">
        <div class="fw-bold" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif; color: #504538; font-size: 1.1rem;">Settings</div>
        <small style="color: #627e71; font-family: 'Lucida Fax', serif; font-size: 0.85rem;">Inventory • Categories • Configuration</small>
    </div>

    {{-- ================= TAB NAV ================= --}}
    <div class="px-3 pt-3 bg-white border-bottom">

        <ul class="nav nav-pills nav-fill gap-2 fw-semibold coffeeshop-nav-pills app-tabs" id="settingsTabs" role="tablist" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif;">

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

        <div class="tab-content p-3">

            {{-- ================= INVENTORY SETTINGS ================= --}}
            <div class="tab-pane fade show active" id="tab-inventory">

                <form action="{{ route('coffeeshop.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <label class="form-label fw-bold" style="font-family: 'Lucida Fax', 'Georgia', serif; color: #504538;">Default Low Stock Threshold</label>

                    <input type="number"
                           min="1"
                           name="default_low_stock_threshold"
                           class="form-control mb-2 shadow-none"
                           value="{{ $defaultLowStockThreshold }}"
                           required
                           style="border: 1px solid #827567; border-radius: 0.5rem; max-width: 380px; font-family: 'Lucida Fax', 'Georgia', serif; font-size: 1rem;">

                    <small class="d-block mb-4" style="color: #827567; font-family: 'Lucida Fax', 'Georgia', serif; font-size: 0.88rem;">
                        Used when a product has no custom threshold.
                        Walk-in folio ID: {{ $walkInFolioId ?? 'Not configured' }}
                    </small>

                    <button class="btn text-white rounded-pill px-4 fw-semibold shadow-sm" style="background-color: #334c42; border: none; font-family: 'Lucida Fax', serif; font-size: 0.95rem;">
                        Save Settings
                    </button>
                </form>

            </div>

            {{-- ================= ADD CATEGORY ================= --}}
            <div class="tab-pane fade" id="tab-add-category">

                <form action="{{ route('coffeeshop.settings.categories.store') }}" method="POST">
                    @csrf

                    <label class="form-label fw-bold" style="font-family: 'Lucida Fax', 'Georgia', serif; color: #504538;">Category Name</label>
                    <input type="text" name="name" class="form-control mb-3 shadow-none" required style="border: 1px solid #827567; border-radius: 0.5rem; max-width: 380px; font-family: 'Lucida Fax', 'Georgia', serif; font-size: 1rem;">

                    <label class="form-label fw-bold" style="font-family: 'Lucida Fax', 'Georgia', serif; color: #504538;">Sort Order</label>
                    <input type="number" min="0" name="sort_order" class="form-control mb-4 shadow-none" value="0" style="border: 1px solid #827567; border-radius: 0.5rem; max-width: 380px; font-family: 'Lucida Fax', 'Georgia', serif; font-size: 1rem;">

                    <button class="btn text-white rounded-pill px-4 fw-semibold shadow-sm" style="background-color: #334c42; border: none; font-family: 'Lucida Fax', serif; font-size: 0.95rem;">
                        Add Category
                    </button>
                </form>

            </div>

            {{-- ================= CATEGORIES TABLE ================= --}}
            <div class="tab-pane fade" id="tab-categories">

                <div class="table-responsive p-3 border rounded-4 bg-white overflow-hidden shadow-sm">

                    <table class="table align-middle mb-0 coffeeshop-table">

                        <thead style="background-color: #f8f3ed; border-bottom: 1px solid #e5e7eb; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                            <tr>
                                <th class="ps-4" style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">NAME</th>
                                <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">SORT</th>
                                <th style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">STATUS</th>
                                <th class="pe-4 text-end" style="color: #2c241d; font-size: 0.90rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; padding: 1rem 1rem;">ACTION</th>
                            </tr>
                        </thead>

                        <tbody style="font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">

                            @forelse($categories as $category)
                                <tr style="border-bottom: 1px solid #f0f0f0;">
                                    <td class="ps-4" style="padding: 1.05rem 1rem; color: #2c241d; font-weight: 600; font-size: 1.05rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">{{ $category->name }}</td>
                                    <td style="padding: 1.05rem 1rem; color: #554d46; font-size: 0.98rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">{{ $category->sort_order }}</td>
                                    <td style="padding: 1.05rem 1rem;">
                                        <span class="coffeeshop-pill fw-semibold {{ $category->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}" style="font-size: 0.88rem; padding: 0.28rem 0.8rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4" style="padding: 1.05rem 1rem;">

                                        <form action="{{ route('coffeeshop.settings.categories.toggle', $category) }}"
                                              method="POST">
                                            @csrf
                                            @method('PATCH')

                                            <button class="btn btn-sm rounded-pill px-3 fw-semibold" style="border: 1px solid #827567; color: #382e25; font-size: 0.88rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                                {{ $category->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="fa-solid fa-folder-open fa-3x text-muted mb-3"></i>
                                        <div class="fw-bold" style="color: #2c241d; font-size: 1.1rem; font-family: 'Plus Jakarta Sans', 'Inter', 'Segoe UI', sans-serif;">
                                            No categories found.
                                        </div>
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
</div>
@endsection
