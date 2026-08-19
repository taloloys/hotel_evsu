@extends('layouts.app')

@section('title', 'Add Product')
@section('pageTitle', 'Add Product')
@section('pageSubtitle', 'Create a new POS menu item')

@section('content')
@include('coffeeshop.partials.alerts')

<div class="coffeeshop-page-shell">
    <div class="coffeeshop-hero">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
            <div>
                <div class="fw-bold fs-5">Add a new menu item</div>
                <div class="opacity-75 mt-1">Create polished product entries with the same coffee-shop styling used throughout the system.</div>
            </div>
        </div>
    </div>

    <div class="coffeeshop-panel">
        <div class="p-4">
            <form action="{{ route('coffeeshop.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('coffeeshop.products.form')
                <div class="mt-4 d-flex gap-2">
                    <button class="btn text-white rounded-pill px-4 font-brand fw-bold" style="background-color: #334c42; border: none; font-family: 'Lucida Fax', serif;">Save Product</button>
                    <a href="{{ route('coffeeshop.products') }}" class="btn rounded-pill px-4 font-brand" style="border: 1px solid #827567; color: #504538; font-family: 'Lucida Fax', serif;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
