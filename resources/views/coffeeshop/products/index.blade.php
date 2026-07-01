@extends('layouts.app')

@section('title', 'Products')
@section('pageTitle', 'Product Management')
@section('pageSubtitle', 'Manage menu items, pricing, stock, and categories')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="coffeeshop-page-shell">
    <div class="coffeeshop-hero">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
            <div>
                <div class="fw-bold fs-5">Menu design and product control</div>
                <div class="opacity-75 mt-1">Fine-tune your drinks, pricing, and availability from one calm workspace.</div>
            </div>
            <a href="{{ route('coffeeshop.products.create') }}" class="btn btn-light rounded-pill px-3 fw-semibold">
                <i class="fa-solid fa-plus me-2"></i>Add Product
            </a>
        </div>
    </div>

    <div class="coffeeshop-panel p-3 p-lg-4">
        <form class="row g-2 align-items-end mb-3" method="GET">
            <div class="col-lg-6">
                <label class="form-label small text-muted mb-1">Search</label>
                <div class="input-group coffeeshop-form-control">
                    <span class="input-group-text bg-white border-0">
                        <i class="fa-solid fa-magnifying-glass text-muted"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-0" placeholder="Search products..." onkeydown="if(event.key==='Enter'){ this.form.submit(); }">
                </div>
            </div>
            <div class="col-lg-4">
                <label class="form-label small text-muted mb-1">Category</label>
                <select name="category_id" class="form-select coffeeshop-form-control" onchange="this.form.submit()">
                    <option value="all">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}" @selected(request('category_id') == $category->category_id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table mb-0 align-middle coffeeshop-table">
                    <thead><tr><th>Product</th><th>Category</th><th>Description</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="fw-semibold text-brown">{{ $product->name }}</div>
                                @if($product->isLowStock())<span class="coffeeshop-pill bg-danger-subtle text-danger">Low Stock</span>@endif
                            </td>
                            <td>{{ $product->category?->name }}</td>
                            <td>{{ Str::limit($product->description, 50) }}</td>
                            <td>₱{{ number_format($product->price, 2) }}</td>
                            <td>{{ $product->stock_quantity }}</td>
                            <td><span class="coffeeshop-pill {{ $product->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('coffeeshop.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Edit</a>
                                @if($product->is_active)
                                <form action="{{ route('coffeeshop.products.destroy', $product) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Deactivate this product?')">Deactivate</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted text-center py-4">No products found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($products->hasPages())
            <div class="card-footer bg-white border-0">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
