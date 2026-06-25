@extends('layouts.app')

@section('title', 'POS Settings')
@section('pageTitle', 'POS Settings')
@section('pageSubtitle', 'Configure inventory alerts and product categories')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Inventory Settings</div>
            <div class="card-body">
                <form action="{{ route('coffeeshop.settings.update') }}" method="POST">
                    @csrf @method('PUT')
                    <label class="form-label">Default Low Stock Threshold</label>
                    <input type="number" min="1" name="default_low_stock_threshold" class="form-control mb-2" value="{{ $defaultLowStockThreshold }}" required>
                    <small class="text-muted d-block mb-3">Used when a product has no custom threshold. Walk-in folio ID: {{ $walkInFolioId ?? 'Not configured' }}</small>
                    <button class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Add Category</div>
            <div class="card-body">
                <form action="{{ route('coffeeshop.settings.categories.store') }}" method="POST">
                    @csrf
                    <label class="form-label">Category Name</label>
                    <input type="text" name="name" class="form-control mb-2" required>
                    <label class="form-label">Sort Order</label>
                    <input type="number" min="0" name="sort_order" class="form-control mb-3" value="0">
                    <button class="btn btn-outline-primary">Add Category</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Categories</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Name</th><th>Sort</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->sort_order }}</td>
                            <td>{{ $category->is_active ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <form action="{{ route('coffeeshop.settings.categories.toggle', $category) }}" method="POST">@csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-secondary">{{ $category->is_active ? 'Deactivate' : 'Activate' }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
