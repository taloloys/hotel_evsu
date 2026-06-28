@extends('layouts.app')

@section('title', 'Products')
@section('pageTitle', 'Product Management')
@section('pageSubtitle', 'Manage menu items, pricing, stock, and categories')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="d-flex justify-content-between align-items-center mb-3">
    <form class="row g-2" method="GET">
        <div class="col-auto">

            <div class="input-group" style="width: 450px; border: 1px solid;">
                <span class="input-group-text bg-white">
                    <i class="fa-solid fa-magnifying-glass text-muted"></i>
                </span>

                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="Search products..."
                    onkeydown="if(event.key==='Enter'){ this.form.submit(); }">
            </div>
        </div>
        <div class="col-auto">
            <select name="category_id"
                    class="form-select"
                    style="width: 220px; border: 1px solid;"
                    onchange="this.form.submit()">

                <option value="all">All Categories</option>

                @foreach($categories as $category)
                    <option value="{{ $category->category_id }}"
                        @selected(request('category_id') == $category->category_id)>
                        {{ $category->name }}
                    </option>
                @endforeach

            </select>
        </div>
    </form>
    <a href="{{ route('coffeeshop.products.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Add Product</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead><tr><th>Product</th><th>Category</th><th>Description</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($products as $product)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $product->name }}</div>
                        @if($product->isLowStock())<span class="badge bg-danger">Low Stock</span>@endif
                    </td>
                    <td>{{ $product->category?->name }}</td>
                    <td>{{ Str::limit($product->description, 50) }}</td>
                    <td>₱{{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->stock_quantity }}</td>
                    <td>{{ $product->is_active ? 'Active' : 'Inactive' }}</td>
                    <td class="text-end">
                        <a href="{{ route('coffeeshop.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        @if($product->is_active)
                        <form action="{{ route('coffeeshop.products.destroy', $product) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Deactivate this product?')">Deactivate</button>
                        </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-muted">No products found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
    <div class="card-footer">{{ $products->links() }}</div>
    @endif
</div>
@endsection
