<div>

    <!-- HEADER -->
    <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">

        <img src="{{ asset('images/logo.png') }}" alt="Don Felipe Hotel Logo" class="me-3"
            style="width:80px; height:70px; object-fit:contain;">

        <div>
            <h5 class="text-white fw-bold mb-0">Don Felipe Hotel</h5>
            <small class="text-secondary">Administrator</small>
        </div>

    </div>

    <!-- ADMIN PANEL -->
    @canany(['manage-users', 'manage-shifts'])
        <div class="text-uppercase text-secondary small fw-bold mb-2">
            Admin Control
        </div>

        <nav class="nav flex-column mb-3">
            @can('manage-users')
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line me-2"></i>
                    Dashboard
                </a>

                <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-group me-2"></i>
                    Users
                </a>

                <a href="{{ route('admin.roles') }}" class="nav-link {{ request()->routeIs('admin.roles') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-shield me-2"></i>
                    Roles
                </a>

                <a href="{{ route('admin.permissions') }}"
                    class="nav-link {{ request()->routeIs('admin.permissions') ? 'active' : '' }}">
                    <i class="fa-solid fa-key me-2"></i>
                    Permissions
                </a>

                <a href="{{ route('admin.rooms') }}" class="nav-link {{ request()->routeIs('admin.rooms') ? 'active' : '' }}">
                    <i class="fa-solid fa-bed me-2"></i>
                    Rooms
                </a>

                <a href="{{ route('admin.chargecodes') }}"
                    class="nav-link {{ request()->routeIs('admin.chargecodes') ? 'active' : '' }}">
                    <i class="fa-solid fa-receipt me-2"></i>
                    Charge Codes
                </a>
            @endcan

            @can('manage-shifts')
                <a href="{{ route('admin.shift-schedules') }}"
                    class="nav-link {{ (request()->routeIs('admin.shift-schedules*') && !request()->routeIs('admin.shift-sales*')) ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-check me-2"></i>
                    Shift Schedules
                </a>

                <a href="{{ route('admin.shift-sales') }}"
                    class="nav-link {{ request()->routeIs('admin.shift-sales*') ? 'active' : '' }}">
                    <i class="fa-solid fa-cash-register me-2"></i>
                    Shift Sales
                </a>
            @endcan

            @can('manage-users')
                <a href="{{ route('admin.activitylogs') }}"
                    class="nav-link {{ request()->routeIs('admin.activitylogs') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i>
                    Activity Logs
                </a>
            @endcan
        </nav>
    @endcanany

    <!-- FRONT DESK -->
    @canany(['manage-reservations', 'view-folio'])
        <hr class="border-secondary opacity-25 my-3">

        <div class="text-uppercase text-secondary small fw-bold mb-2">
            Front Desk
        </div>

        <nav class="nav flex-column mb-3">
            @can('manage-reservations')
                <a href="{{ route('frontdesk.dashboard') }}"
                    class="nav-link {{ request()->routeIs('frontdesk.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-hotel me-2"></i>
                    Dashboard
                </a>

                <a href="{{ route('frontdesk.reservation') }}"
                    class="nav-link {{ request()->routeIs('frontdesk.reservation') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-check me-2"></i>
                    Reservation
                </a>

                <a href="{{ route('frontdesk.registration') }}"
                    class="nav-link {{ request()->routeIs('frontdesk.registration') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-plus me-2"></i>
                    Registration
                </a>
            @endcan

            @can('view-folio')
                <a href="{{ route('frontdesk.guest-list') }}"
                    class="nav-link {{ request()->routeIs('frontdesk.guest-list') ? 'active' : '' }}">
                    <i class="fa-solid fa-users me-2"></i>
                    Guest List
                </a>

                <a href="{{ route('frontdesk.guest-folio') }}"
                    class="nav-link {{ request()->routeIs('frontdesk.guest-folio') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar me-2"></i>
                    Guest Folio
                </a>

                <a href="{{ route('frontdesk.shift-sales') }}"
                    class="nav-link {{ request()->routeIs('frontdesk.shift-sales') ? 'active' : '' }}">
                    <i class="fa-solid fa-cash-register me-2"></i>
                    Shift Sales
                </a>
            @endcan
        </nav>
    @endcanany

    <!-- COFFEE SHOP -->
    @can('manage-inventory')
        <hr class="border-secondary opacity-25 my-3">

        <div class="text-uppercase text-secondary small fw-bold mb-2">
            Coffee Shop
        </div>

        <nav class="nav flex-column mb-3">
            <a href="{{ route('coffeeshop.dashboard') }}"
                class="nav-link {{ request()->routeIs('coffeeshop.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge me-2"></i>
                Dashboard
            </a>

            <a href="{{ route('coffeeshop.pos') }}"
                class="nav-link {{ request()->routeIs('coffeeshop.pos') ? 'active' : '' }}">
                <i class="fa-solid fa-cash-register me-2"></i>
                POS
            </a>

            <a href="{{ route('coffeeshop.orders') }}"
                class="nav-link {{ request()->routeIs('coffeeshop.orders') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt me-2"></i>
                Orders
            </a>

            <a href="{{ route('coffeeshop.sales') }}"
                class="nav-link {{ request()->routeIs('coffeeshop.sales') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line me-2"></i>
                Sales
            </a>

            <a href="{{ route('coffeeshop.inventory') }}"
                class="nav-link {{ request()->routeIs('coffeeshop.inventory') ? 'active' : '' }}">
                <i class="fa-solid fa-box-open me-2"></i>
                Inventory
            </a>
        </nav>
    @endcan

    <!-- ACCOUNTING -->
    @can('view-folio')
        <hr class="border-secondary opacity-25 my-3">

        <div class="text-uppercase text-secondary small fw-bold mb-2">
            Accounting
        </div>

        <nav class="nav flex-column mb-3">
            <a href="{{ route('accounting.dashboard') }}"
                class="nav-link {{ request()->routeIs('accounting.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie me-2"></i>
                Dashboard
            </a>

            <a href="{{ route('accounting.billing') }}"
                class="nav-link {{ request()->routeIs('accounting.billing') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice me-2"></i>
                Billing
            </a>

            <a href="{{ route('accounting.payments') }}"
                class="nav-link {{ request()->routeIs('accounting.payments') ? 'active' : '' }}">
                <i class="fa-solid fa-credit-card me-2"></i>
                Payments
            </a>

            <a href="{{ route('accounting.receivables') }}"
                class="nav-link {{ request()->routeIs('accounting.receivables') ? 'active' : '' }}">
                <i class="fa-solid fa-hand-holding-dollar me-2"></i>
                Receivables
            </a>

            <a href="{{ route('accounting.expenses') }}"
                class="nav-link {{ request()->routeIs('accounting.expenses') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt me-2"></i>
                Expenses
            </a>

            <a href="{{ route('accounting.reports') }}"
                class="nav-link {{ request()->routeIs('accounting.reports') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-bar me-2"></i>
                Reports
            </a>

            <a href="{{ route('accounting.audit') }}"
                class="nav-link {{ request()->routeIs('accounting.audit') ? 'active' : '' }}">
                <i class="fa-solid fa-shield-halved me-2"></i>
                Audit Logs
            </a>
        </nav>
    @endcan

</div>