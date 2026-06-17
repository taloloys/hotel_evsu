<div class="p-4">

    <div class="mb-5">

        <h3 class="text-white fw-bold">
            <i class="fa-solid fa-hotel me-2"></i>
            Don Felipe Hotel
        </h3>

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