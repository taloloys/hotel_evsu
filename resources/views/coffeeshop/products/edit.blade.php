@extends('layouts.app')

@section('title', 'Edit Product')
@section('pageTitle', 'Edit Product')
@section('pageSubtitle', $product->name)

@section('content')
@include('coffeeshop.partials.alerts')

<div class="coffeeshop-page-shell">
    <div class="coffeeshop-hero">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
            <div>
                <div class="fw-bold fs-5">Edit menu item</div>
                <div class="opacity-75 mt-1">Update the product details while keeping the experience consistent with the rest of the coffee shop workspace.</div>
            </div>
        </div>
    </div>

    <div class="coffeeshop-panel">
        <div class="p-4">
            <form action="{{ route('coffeeshop.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                @include('coffeeshop.products.form', ['product' => $product])
                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary rounded-pill px-4">Update Product</button>
                    <a href="{{ route('coffeeshop.products') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
