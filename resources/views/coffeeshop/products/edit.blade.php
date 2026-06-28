@extends('layouts.app')

@section('title', 'Edit Product')
@section('pageTitle', 'Edit Product')
@section('pageSubtitle', $product->name)

@section('content')
@include('coffeeshop.partials.alerts')

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('coffeeshop.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('coffeeshop.products.form', ['product' => $product])
            <br>
            <button class="btn btn-primary">Update Product</button>
            <a href="{{ route('coffeeshop.products') }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
