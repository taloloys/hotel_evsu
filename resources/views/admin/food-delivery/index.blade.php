@extends('layouts.app')

@section('title', 'Food Delivery')
@section('pageTitle', 'Food Delivery')
@section('pageSubtitle', 'Order food from your preferred delivery platform')

@push('styles')
<style>
    .food-delivery-hero {
        text-align: center;
        padding: 2.5rem 1rem 1.5rem;
    }

    .food-delivery-hero h2 {
        font-size: 2rem;
        font-weight: 800;
        background: linear-gradient(135deg, #ff2d6e, #ff8c00, #e91e8c);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.4rem;
    }

    .food-delivery-hero p {
        color: #8a7060;
        font-size: 1rem;
    }

    .platform-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        max-width: 820px;
        margin: 2rem auto 0;
        padding: 0 1rem;
    }

    @media (max-width: 600px) {
        .platform-grid { grid-template-columns: 1fr; }
    }

    .platform-card {
        position: relative;
        border-radius: 24px;
        overflow: hidden;
        cursor: pointer;
        text-decoration: none;
        display: block;
        transition: transform 0.32s cubic-bezier(.34,1.56,.64,1), box-shadow 0.3s ease;
        border: 1.5px solid transparent;
    }

    .platform-card:hover {
        transform: translateY(-8px) scale(1.02);
        text-decoration: none;
    }

    .card-foodpanda {
        background: linear-gradient(145deg, #fff0f5 0%, #ffe4ee 60%, #ffd6e7 100%);
        box-shadow: 0 8px 32px rgba(217, 0, 88, 0.12);
        border-color: rgba(217, 0, 88, 0.15);
    }
    .card-foodpanda:hover {
        box-shadow: 0 20px 50px rgba(217, 0, 88, 0.25);
        border-color: rgba(217, 0, 88, 0.35);
    }

    .card-grab {
        background: linear-gradient(145deg, #f0fff4 0%, #dcffe8 60%, #c8f7d8 100%);
        box-shadow: 0 8px 32px rgba(0, 172, 67, 0.12);
        border-color: rgba(0, 172, 67, 0.15);
    }
    .card-grab:hover {
        box-shadow: 0 20px 50px rgba(0, 172, 67, 0.25);
        border-color: rgba(0, 172, 67, 0.35);
    }

    .card-inner {
        padding: 2.8rem 2rem 2.4rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.2rem;
    }

    .card-logo-wrap {
        width: 90px;
        height: 90px;
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.8rem;
        transition: transform 0.3s ease;
    }

    .platform-card:hover .card-logo-wrap {
        transform: scale(1.12) rotate(-4deg);
    }

    .logo-foodpanda {
        background: linear-gradient(135deg, #d70060, #ff4d94);
        box-shadow: 0 6px 20px rgba(215, 0, 96, 0.35);
        color: #fff;
    }

    .logo-grab {
        background: linear-gradient(135deg, #00b14f, #00d160);
        box-shadow: 0 6px 20px rgba(0, 177, 79, 0.35);
        color: #fff;
    }

    .card-name {
        font-size: 1.45rem;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .name-foodpanda { color: #c00057; }
    .name-grab      { color: #00873c; }

    .card-desc {
        font-size: 0.88rem;
        color: #6b7280;
        text-align: center;
        line-height: 1.5;
        margin: 0;
    }

    .card-btn {
        margin-top: 0.4rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.6rem;
        border-radius: 50px;
        font-size: 0.88rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        transition: all 0.25s ease;
    }

    .btn-foodpanda {
        background: linear-gradient(135deg, #d70060, #ff4d94);
        color: #fff;
        box-shadow: 0 4px 14px rgba(215, 0, 96, 0.3);
    }
    .btn-foodpanda:hover { box-shadow: 0 6px 20px rgba(215, 0, 96, 0.5); color: #fff; }

    .btn-grab {
        background: linear-gradient(135deg, #00b14f, #00d160);
        color: #fff;
        box-shadow: 0 4px 14px rgba(0, 177, 79, 0.3);
    }
    .btn-grab:hover { box-shadow: 0 6px 20px rgba(0, 177, 79, 0.5); color: #fff; }

    .platform-card .card-badge {
        position: absolute;
        top: 16px;
        right: 16px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 0.25rem 0.7rem;
        border-radius: 50px;
    }

    .badge-foodpanda { background: rgba(215, 0, 96, 0.12); color: #c00057; }
    .badge-grab      { background: rgba(0, 177, 79, 0.12); color: #00873c; }

    .card-blob {
        position: absolute;
        border-radius: 50%;
        opacity: 0.12;
        pointer-events: none;
    }
    .blob-foodpanda { background: #d70060; width: 160px; height: 160px; bottom: -40px; left: -40px; }
    .blob-grab      { background: #00b14f; width: 160px; height: 160px; bottom: -40px; right: -40px; }

    .info-strip {
        max-width: 820px;
        margin: 2rem auto 0;
        padding: 0 1rem 2rem;
    }

    .info-card {
        background: rgba(255,255,255,0.65);
        border: 1px solid rgba(78,52,46,0.08);
        border-radius: 16px;
        padding: 1.1rem 1.6rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        color: #7a6050;
        font-size: 0.875rem;
        backdrop-filter: blur(6px);
    }

    .info-card i { font-size: 1.1rem; color: var(--caramel); flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="food-delivery-hero">
    <h2><i class="fa-solid fa-utensils me-2" style="-webkit-text-fill-color: inherit;"></i> Food Delivery</h2>
    <p>Choose your preferred food delivery platform to place an order.</p>
</div>

<div class="platform-grid">

    <a href="https://www.foodpanda.ph/" target="_blank" rel="noopener noreferrer"
       class="platform-card card-foodpanda" id="btn-foodpanda">
        <div class="card-blob blob-foodpanda"></div>
        <span class="card-badge badge-foodpanda">
            <i class="fa-solid fa-circle-dot me-1"></i> Available
        </span>
        <div class="card-inner">
            <div class="card-logo-wrap logo-foodpanda">
                <i class="fa-solid fa-motorcycle"></i>
            </div>
            <div class="card-name name-foodpanda">Food Panda</div>
            <p class="card-desc">
                Fast food delivery right to your doorstep.<br>Wide selection of restaurants nationwide.
            </p>
            <span class="card-btn btn-foodpanda">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                Order Now
            </span>
        </div>
    </a>

    <a href="https://www.grab.com/ph/food/" target="_blank" rel="noopener noreferrer"
       class="platform-card card-grab" id="btn-grabfood">
        <div class="card-blob blob-grab"></div>
        <span class="card-badge badge-grab">
            <i class="fa-solid fa-circle-dot me-1"></i> Available
        </span>
        <div class="card-inner">
            <div class="card-logo-wrap logo-grab">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <div class="card-name name-grab">GrabFood</div>
            <p class="card-desc">
                Reliable delivery powered by Grab.<br>Track your order in real-time, fast &amp; easy.
            </p>
            <span class="card-btn btn-grab">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                Order Now
            </span>
        </div>
    </a>

</div>

<div class="info-strip">
    <div class="info-card">
        <i class="fa-solid fa-circle-info"></i>
        <span>Click a platform card to open the food delivery website in a new tab.</span>
    </div>
</div>
@endsection
