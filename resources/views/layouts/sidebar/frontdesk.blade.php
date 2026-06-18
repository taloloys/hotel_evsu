<div class="p-4">

    <!-- HEADER -->
    <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">

        <img src="{{ asset('images/logo.png') }}"
             alt="Don Felipe Hotel Logo"
             class="me-3"
             style="width:80px; height:70px; object-fit:contain;">

        <div>
            <h5 class="text-white fw-bold mb-2">
                Don Felipe Hotel
            </h5>

            <small class="text-secondary">
                Front Desk Panel
            </small>
        </div>

    </div>

    <!-- MENU TITLE -->
    <div class="text-uppercase text-secondary small fw-bold mb-2">
        Main Menu
    </div>

    <nav class="nav flex-column">

        <!-- DASHBOARD -->
        <a href="{{ route('frontdesk.dashboard') }}"
           class="nav-link {{ request()->routeIs('frontdesk.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line me-2"></i>
            Dashboard
        </a>

        <!-- RESERVATION -->
        <a href="{{ route('frontdesk.reservation') }}"
           class="nav-link {{ request()->routeIs('frontdesk.reservation') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-check me-2"></i>
            Reservation
        </a>

        <!-- REGISTRATION -->
        <a href="{{ route('frontdesk.registration') }}"
           class="nav-link {{ request()->routeIs('frontdesk.registration') ? 'active' : '' }}">
            <i class="fa-solid fa-user-plus me-2"></i>
            Registration
        </a>

        <!-- GUEST LIST -->
        <a href="{{ route('frontdesk.guest-list') }}"
           class="nav-link {{ request()->routeIs('frontdesk.guest-list') ? 'active' : '' }}">
            <i class="fa-solid fa-users me-2"></i>
            Guest List
        </a>

        <!-- GUEST FOLIO -->
        <a href="{{ route('frontdesk.guest-folio') }}"
           class="nav-link {{ request()->routeIs('frontdesk.guest-folio') ? 'active' : '' }}">
            <i class="fa-solid fa-file-invoice-dollar me-2"></i>
            Guest Folio
        </a>

        <hr class="border-secondary opacity-25 my-3">

        <!-- FINANCIAL -->
        <div class="text-uppercase text-secondary small fw-bold mb-2">
            Financial
        </div>

        <!-- SHIFT SALES -->
        <a href="{{ route('frontdesk.shift-sales') }}"
           class="nav-link {{ request()->routeIs('frontdesk.shift-sales') ? 'active' : '' }}">
            <i class="fa-solid fa-cash-register me-2"></i>
            Shift Sales
        </a>

    </nav>

</div>