<div class="p-4">

    <!-- HEADER -->
    <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">

        <img src="{{ asset('images/logo.png') }}"
             alt="Don Felipe Coffee Shop Logo"
             class="me-3"
             style="width:80px; height:70px; object-fit:contain;">

        <div>
            <h5 class="text-white fw-bold mb-0">
                Don Felipe Coffee Shop
            </h5>

            <small class="text-secondary">
                POS System
            </small>
        </div>

    </div>

    <!-- MENU TITLE -->
    @can('manage-inventory')
    <div class="text-uppercase text-secondary small fw-bold mb-2">
        Main Menu
    </div>

    <nav class="nav flex-column">

        <!-- DASHBOARD -->
        <a href="{{ route('coffeeshop.dashboard') }}"
           class="nav-link {{ request()->routeIs('coffeeshop.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge me-2"></i>
            Dashboard
        </a>

        <!-- POS -->
        <a href="{{ route('coffeeshop.pos') }}"
           class="nav-link {{ request()->routeIs('coffeeshop.pos') ? 'active' : '' }}">
            <i class="fa-solid fa-cash-register me-2"></i>
            POS
        </a>

        <!-- ORDERS -->
        <a href="{{ route('coffeeshop.orders') }}"
           class="nav-link {{ request()->routeIs('coffeeshop.orders') ? 'active' : '' }}">
            <i class="fa-solid fa-receipt me-2"></i>
            Orders
        </a>

        <!-- SALES -->
        <a href="{{ route('coffeeshop.sales') }}"
           class="nav-link {{ request()->routeIs('coffeeshop.sales') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line me-2"></i>
            Sales
        </a>
    </nav>
    @endcan

</div>