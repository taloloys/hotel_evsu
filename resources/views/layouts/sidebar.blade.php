<div class="p-4">

    <!-- HEADER -->
    <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
        <img src="{{ asset('images/logo.png') }}"
             alt="Don Felipe Hotel Logo"
             class="me-3"
             style="width:80px; height:70px; object-fit:contain;">
        <div>
            <h5 class="text-white fw-bold mb-1">
                Don Felipe Hotel
            </h5>
            <small class="text-secondary">
                {{ auth()->user()?->role?->role_name === 'ADMIN' ? 'Administrator' : (auth()->user()?->role?->description ?? 'Staff') }}
            </small>
        </div>
    </div>

    <!-- SECTIONS -->
    <div class="sidebar-sections">
        
        <!-- ADMIN CONTROL -->
        @canany(['manage-users', 'manage-shifts'])
        <div class="menu-section mb-4">
            <div class="text-uppercase text-secondary small fw-bold mb-2">
                Admin Control
            </div>
            <nav class="nav flex-column">
                @can('manage-users')
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line me-2"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.users') }}"
                   class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-group me-2"></i>
                    Users
                </a>
                <a href="{{ route('admin.roles') }}"
                   class="nav-link {{ request()->routeIs('admin.roles') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-shield me-2"></i>
                    Roles
                </a>
                <a href="{{ route('admin.permissions') }}"
                   class="nav-link {{ request()->routeIs('admin.permissions') ? 'active' : '' }}">
                    <i class="fa-solid fa-key me-2"></i>
                    Permissions
                </a>
                <a href="{{ route('admin.rooms') }}"
                   class="nav-link {{ request()->routeIs('admin.rooms') ? 'active' : '' }}">
                    <i class="fa-solid fa-bed me-2"></i>
                    Rooms
                </a>
                <a href="{{ route('admin.landing-page') }}"
                   class="nav-link {{ request()->routeIs('admin.landing-page*') ? 'active' : '' }}">
                    <i class="fa-solid fa-globe me-2"></i>
                    Landing Page
                </a>
                <a href="{{ route('admin.chargecodes') }}"
                   class="nav-link {{ request()->routeIs('admin.chargecodes') ? 'active' : '' }}">
                    <i class="fa-solid fa-receipt me-2"></i>
                    Charge Codes
                </a>
                <a href="{{ route('admin.credit-accounts') }}"
                   class="nav-link {{ request()->routeIs('admin.credit-accounts*') ? 'active' : '' }}">
                    <i class="fa-solid fa-building-columns me-2"></i>
                    Credit Accounts
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
                <a href="{{ route('admin.pos-approvals') }}"
                   class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('admin.pos-approvals*') ? 'active' : '' }}">
                    <span><i class="fa-solid fa-check-double me-2"></i>POS Approvals</span>
                    <span id="sidebar-pos-approvals-badge" class="badge bg-danger ms-1 d-none"></span>
                </a>
                <a href="{{ route('admin.backup-restore') }}"
                   class="nav-link {{ request()->routeIs('admin.backup-restore*') ? 'active' : '' }}">
                    <i class="fa-solid fa-hard-drive me-2"></i>
                    Backup & Restore
                </a>
                @endcan
                @if(auth()->user()?->isSuperAdmin())
                <a href="{{ route('admin.sidebar-settings') }}"
                   class="nav-link {{ request()->routeIs('admin.sidebar-settings*') ? 'active' : '' }}">
                    <i class="fa-solid fa-sliders-h me-2"></i>
                    Sidebar Settings
                </a>
                @endif            </nav>
        </div>
        @endcanany

        <!-- FRONT DESK -->
        @if(auth()->user()?->isModuleVisibleInSidebar('frontdesk'))
            @canany(['manage-reservations', 'view-guest-list', 'view-guest-folio', 'view-shift-sales'])
            <div class="menu-section mb-4">
                <div class="text-uppercase text-secondary small fw-bold mb-2">
                    Front Desk
                </div>
            <nav class="nav flex-column">
                @can('manage-reservations')
                <a href="{{ route('frontdesk.dashboard') }}"
                   class="nav-link {{ request()->routeIs('frontdesk.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line me-2"></i>
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
                <a href="{{ route('frontdesk.checkin') }}"
                   class="nav-link {{ request()->routeIs('frontdesk.checkin') ? 'active' : '' }}">
                    <i class="fa-solid fa-key me-2"></i>Check In
                </a>
                @endcan

                @can('view-guest-list')
                <a href="{{ route('frontdesk.guest-list') }}"
                   class="nav-link {{ request()->routeIs('frontdesk.guest-list') ? 'active' : '' }}">
                    <i class="fa-solid fa-users me-2"></i>
                    Guest List
                </a>
                @endcan

                @can('view-guest-folio')
                <a href="{{ route('frontdesk.guest-folio') }}"
                   class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('frontdesk.guest-folio') ? 'active' : '' }}">
                    <span><i class="fa-solid fa-file-invoice-dollar me-2"></i>Guest Folio</span>
                    <span id="sidebar-pending-checkouts-badge" class="badge bg-primary ms-1 d-none"></span>
                </a>
                @endcan

                @can('view-shift-sales')
                <a href="{{ route('frontdesk.shift-sales') }}"
                   class="nav-link {{ request()->routeIs('frontdesk.shift-sales') ? 'active' : '' }}">
                    <i class="fa-solid fa-cash-register me-2"></i>
                    Shift Sales
                </a>
                @endcan
            </nav>
        </div>
            @endcanany
        @endif

        <!-- COFFEE SHOP -->
        @if(auth()->user()?->isModuleVisibleInSidebar('coffeeshop'))
            @can('manage-inventory')
            <div class="menu-section mb-4">
            <div class="text-uppercase text-secondary small fw-bold mb-2">
                Coffee Shop
            </div>
            <nav class="nav flex-column coffeeshop-nav">
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
                <a href="{{ route('coffeeshop.products') }}"
                   class="nav-link {{ request()->routeIs('coffeeshop.products*') ? 'active' : '' }}">
                    <i class="fa-solid fa-mug-hot me-2"></i>
                    Products
                </a>
                <a href="{{ route('coffeeshop.inventory') }}"
                   class="nav-link {{ request()->routeIs('coffeeshop.inventory*') ? 'active' : '' }}">
                    <i class="fa-solid fa-box-open me-2"></i>
                    Inventory
                    <span id="sidebar-low-stock-badge" class="badge bg-danger ms-1 d-none"></span>
                </a>
                <a href="{{ route('coffeeshop.tabs') }}"
                   class="nav-link {{ request()->routeIs('coffeeshop.tabs*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bookmark me-2"></i>
                    Tabs
                </a>
                <a href="{{ route('coffeeshop.orders') }}"
                   class="nav-link {{ request()->routeIs('coffeeshop.orders*') ? 'active' : '' }}">
                    <i class="fa-solid fa-receipt me-2"></i>
                    Orders
                </a>
                <a href="{{ route('coffeeshop.customers') }}"
                   class="nav-link {{ request()->routeIs('coffeeshop.customers*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users me-2"></i>
                    Customers
                </a>
                <a href="{{ route('coffeeshop.statistics') }}"
                   class="nav-link {{ request()->routeIs('coffeeshop.statistics*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie me-2"></i>
                    Statistics
                </a>
                <a href="{{ route('coffeeshop.reports') }}"
                   class="nav-link {{ request()->routeIs('coffeeshop.reports*') || request()->routeIs('coffeeshop.sales*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line me-2"></i>
                    Reports
                </a>
                <a href="{{ route('coffeeshop.settings') }}"
                   class="nav-link {{ request()->routeIs('coffeeshop.settings*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear me-2"></i>
                    Settings
                </a>
            </nav>
        </div>
            @endcan
        @endif

        <!-- ACCOUNTING -->
        @if(auth()->user()?->isModuleVisibleInSidebar('accounting'))
            @canany([
            'view-accounting-dashboard',
            'manage-accounting-billing',
            'manage-accounting-payments',
            'manage-accounting-receivables',
            'manage-accounting-expenses',
            'view-accounting-reports',
            'view-accounting-audit'
        ])
        <div class="menu-section mb-4">
            <div class="text-uppercase text-secondary small fw-bold mb-2">
                Accounting
            </div>
            <nav class="nav flex-column">
                @can('view-accounting-dashboard')
                <a href="{{ route('accounting.dashboard') }}"
                   class="nav-link {{ request()->routeIs('accounting.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie me-2"></i>
                    Dashboard
                </a>
                @endcan
                @can('manage-accounting-billing')
                <a href="{{ route('accounting.billing') }}"
                   class="nav-link {{ request()->routeIs('accounting.billing*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice me-2"></i>
                    Billing
                </a>
                @endcan
                @can('manage-accounting-payments')
                <a href="{{ route('accounting.payments') }}"
                   class="nav-link {{ request()->routeIs('accounting.payments') ? 'active' : '' }}">
                    <i class="fa-solid fa-credit-card me-2"></i>
                    Payments
                </a>
                @endcan
                @can('manage-accounting-receivables')
                <a href="{{ route('accounting.receivables') }}"
                   class="nav-link {{ request()->routeIs('accounting.receivables') ? 'active' : '' }}">
                    <i class="fa-solid fa-hand-holding-dollar me-2"></i>
                    Receivables
                </a>
                @endcan
                @can('manage-accounting-expenses')
                <a href="{{ route('accounting.expenses') }}"
                   class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('accounting.expenses') ? 'active' : '' }}">
                    <span><i class="fa-solid fa-receipt me-2"></i>Expenses</span>
                    <span id="sidebar-pending-expenses-badge" class="badge bg-info ms-1 d-none"></span>
                </a>
                @endcan
                @can('view-accounting-reports')
                <a href="{{ route('accounting.reports') }}"
                   class="nav-link {{ request()->routeIs('accounting.reports') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-bar me-2"></i>
                    Reports
                </a>
                @endcan
                @can('view-accounting-audit')
                <a href="{{ route('accounting.audit') }}"
                   class="nav-link {{ request()->routeIs('accounting.audit') ? 'active' : '' }}">
                    <i class="fa-solid fa-shield-halved me-2"></i>
                    Audit Logs
                </a>
                @endcan
            </nav>
        </div>
            @endcanany
        @endif

        <!-- FOOD ORDER -->
        @if(auth()->user()?->isModuleVisibleInSidebar('food_delivery'))
            @can('access-foodpanda')
        <div class="menu-section mb-4">
            <div class="text-uppercase text-secondary small fw-bold mb-2">
                Food Order
            </div>
            <nav class="nav flex-column">
                <a href="{{ route('admin.food-delivery') }}"
                   class="nav-link {{ request()->routeIs('admin.food-delivery') ? 'active' : '' }}">
                    <i class="fa-solid fa-utensils me-2"></i>
                    Food Delivery
                </a>
            </nav>
        </div>
            @endcan
        @endif

    </div>

</div>

<style>
    .menu-section + .menu-section {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 1.5rem;
        margin-top: 1.5rem;
    }
</style>