<div class="p-4">

    <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">

        <img src="{{ asset('images/logo.png') }}"
             alt="Don Felipe Hotel Logo"
             class="me-3"
             style="width:80px; height:70px; object-fit:contain;">

        <div>
            <h5 class="text-white fw-bold mb-2">
                Don Felipe Hotel
            </h5>
        </div>

    </div>

    <div class="text-uppercase text-secondary small fw-bold mb-2">
        Main Menu
    </div>

    <nav class="nav flex-column">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line me-2"></i>
            Dashboard
        </a>

        <!-- Reservation -->
        <a href="{{ route('reservation') }}"
           class="nav-link {{ request()->routeIs('reservation') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-check me-2"></i>
            Reservation
        </a>

        <!-- Registration -->
        <a href="{{ route('registration') }}"
           class="nav-link {{ request()->routeIs('registration') ? 'active' : '' }}">
            <i class="fa-solid fa-user-plus me-2"></i>
            Registration
        </a>

        <!-- Guest List -->
        <a href="{{ route('guest-list') }}"
           class="nav-link {{ request()->routeIs('guest-list') ? 'active' : '' }}">
            <i class="fa-solid fa-users me-2"></i>
            Guest List
        </a>

        <!-- Guest Folio -->
        <a href="{{ route('guest-folio') }}"
           class="nav-link {{ request()->routeIs('guest-folio') ? 'active' : '' }}">
            <i class="fa-solid fa-file-invoice-dollar me-2"></i>
            Guest Folio
        </a>

        <hr class="border-secondary opacity-25 my-3">

        <div class="text-uppercase text-secondary small fw-bold mb-2">
            Financial
        </div>

        <!-- Shift Sales -->
        <a href="{{ route('shift-sales') }}"
           class="nav-link {{ request()->routeIs('shift-sales') ? 'active' : '' }}">
            <i class="fa-solid fa-cash-register me-2"></i>
            Shift Sales
        </a>

    </nav>

</div>