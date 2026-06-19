<div class="p-4">

    <!-- HEADER -->
    <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">

        <img src="{{ asset('images/logo.png') }}"
             alt="Don Felipe Hotel Logo"
             class="me-3"
             style="width:80px; height:70px; object-fit:contain;">

        <div>
            <h5 class="text-white fw-bold mb-0">Don Felipe Hotel</h5>
            <small class="text-secondary">Admin Panel</small>
        </div>

    </div>

    <!-- MENU TITLE -->
    <div class="text-uppercase text-secondary small fw-bold mb-2">
        Main Menu
    </div>

    <nav class="nav flex-column">

        <!-- DASHBOARD -->
        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line me-2"></i>
            Dashboard
        </a>

        <!-- USERS -->
        <a href="{{ route('admin.users') }}"
           class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <i class="fa-solid fa-user-group me-2"></i>
            Users
        </a>

        <!-- ROLES -->
        <a href="{{ route('admin.roles') }}"
           class="nav-link {{ request()->routeIs('admin.roles') ? 'active' : '' }}">
            <i class="fa-solid fa-user-shield me-2"></i>
            Roles
        </a>

        <!-- PERMISSIONS -->
        <a href="{{ route('admin.permissions') }}"
           class="nav-link {{ request()->routeIs('admin.permissions') ? 'active' : '' }}">
            <i class="fa-solid fa-key me-2"></i>
            Permissions
        </a>

        <!-- ROOMS -->
        <a href="{{ route('admin.rooms') }}"
           class="nav-link {{ request()->routeIs('admin.rooms') ? 'active' : '' }}">
            <i class="fa-solid fa-bed me-2"></i>
            Rooms
        </a>

        <hr class="border-secondary opacity-25 my-3">

        <!-- SYSTEM SECTION -->
        <div class="text-uppercase text-secondary small fw-bold mb-2">
            System
        </div>

        <!-- CHARGE CODES -->
        <a href="{{ route('admin.chargecodes') }}"
        class="nav-link {{ request()->routeIs('admin.chargecodes') ? 'active' : '' }}">
            <i class="fa-solid fa-receipt me-2"></i>
            Charge Codes
        </a>

        <!-- SHIFT SCHEDULES -->
        <a href="{{ route('admin.shift-schedules') }}"
        class="nav-link {{ (request()->routeIs('admin.shift-schedules*') && !request()->routeIs('admin.shift-sales*')) ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-check me-2"></i>
            Shift Schedules
        </a>

        <!-- SHIFT SALES -->
        <a href="{{ route('admin.shift-sales') }}"
        class="nav-link {{ request()->routeIs('admin.shift-sales*') ? 'active' : '' }}">
            <i class="fa-solid fa-cash-register me-2"></i>
            Shift Sales
        </a>

        <!-- ACTIVITY LOGS -->
        <a href="{{ route('admin.activitylogs') }}"
        class="nav-link {{ request()->routeIs('admin.activitylogs') ? 'active' : '' }}">
            <i class="fa-solid fa-clock-rotate-left me-2"></i>
            Activity Logs
        </a>

    </nav>

</div>