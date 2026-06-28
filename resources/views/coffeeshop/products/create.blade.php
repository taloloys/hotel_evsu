@extends('layouts.app')

@section('title', 'Add Product')
@section('pageTitle', 'Add Product')
@section('pageSubtitle', 'Create a new POS menu item')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('coffeeshop.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('coffeeshop.products.form')
            <br>
            <button class="btn btn-primary">Save Product</button>
            <a href="{{ route('coffeeshop.products') }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
