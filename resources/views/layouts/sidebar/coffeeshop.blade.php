<div class="p-4 coffeeshop-nav">

    <!-- HEADER -->
    <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">

        <img src="{{ asset('images/logo.png') }}"
             alt="EVSU Coffee Shop Logo"
             class="me-3"
             style="width:80px; height:70px; object-fit:contain;">

        <div>
            <h5 class="text-white fw-bold mb-0 font-display" style="font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif; letter-spacing: 0.02em;">
                EVSU Coffee Shop
            </h5>

            <small class="font-brand" style="color: #d4c5b3; font-family: 'Lucida Fax', 'Georgia', serif;">
                POS System
            </small>
        </div>

    </div>

    <!-- MENU TITLE -->
    @can('manage-inventory')
    <div class="text-uppercase small fw-bold mb-2 font-display" style="color: #d4c5b3; font-family: 'Franklin Gothic Medium', 'Franklin Gothic', sans-serif; letter-spacing: 0.06em;">
        Main Menu
    </div>

    <nav class="nav flex-column">

        <!-- DASHBOARD -->
        <a href="{{ route('coffeeshop.dashboard') }}"
           class="nav-link {{ request()->routeIs('coffeeshop.dashboard') ? 'active' : '' }}"
           style="font-family: 'Lucida Fax', 'Georgia', serif;">
            <i class="fa-solid fa-gauge me-2"></i>
            Dashboard
        </a>

        <!-- POS -->
        <a href="{{ route('coffeeshop.pos') }}"
           class="nav-link {{ request()->routeIs('coffeeshop.pos') ? 'active' : '' }}"
           style="font-family: 'Lucida Fax', 'Georgia', serif;">
            <i class="fa-solid fa-cash-register me-2"></i>
            POS
        </a>

        <!-- ORDERS -->
        <a href="{{ route('coffeeshop.orders') }}"
           class="nav-link {{ request()->routeIs('coffeeshop.orders*') ? 'active' : '' }}"
           style="font-family: 'Lucida Fax', 'Georgia', serif;">
            <i class="fa-solid fa-receipt me-2"></i>
            Orders
        </a>

        <!-- SALES -->
        <a href="{{ route('coffeeshop.sales') }}"
           class="nav-link {{ request()->routeIs('coffeeshop.sales*') ? 'active' : '' }}"
           style="font-family: 'Lucida Fax', 'Georgia', serif;">
            <i class="fa-solid fa-chart-line me-2"></i>
            Sales
        </a>
    </nav>
    @endcan

</div>