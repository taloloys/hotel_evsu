<div class="p-4">

    <!-- HEADER -->
    <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
        <img src="{{ asset('images/logo.png') }}"
             alt="Hospitality & Tourism Management Department Logo"
             class="me-3"
             style="width:80px; height:70px; object-fit:contain;">
        <div>
            <h5 class="fw-bold mb-0 font-display text-white">
                Hospitality & Tourism Management Department
            </h5>
        </div>
    </div>

    <!-- SECTIONS -->
    <div class="sidebar-sections">
        
        <!-- ADMIN CONTROL -->
        @canany(['manage-users', 'manage-admins', 'manage-roles-permissions', 'manage-landing-page', 'manage-backup-restore', 'manage-rooms', 'manage-charge-codes', 'manage-credit-accounts', 'view-activity-logs', 'manage-pos-approvals', 'manage-shifts'])
        <div class="menu-section mb-4">
            @php $isAdminActive = request()->routeIs('admin.*') && !request()->routeIs('admin.food-delivery'); @endphp
            <button class="btn btn-link text-decoration-none w-100 p-0 text-start d-flex justify-content-between align-items-center sidebar-accordion-btn mb-2 {{ $isAdminActive ? '' : 'collapsed' }}"
                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdmin" aria-expanded="{{ $isAdminActive ? 'true' : 'false' }}" aria-controls="collapseAdmin">
                <div class="text-uppercase small fw-bold" style="color: #d4c5b3; font-family: 'Plus Jakarta Sans', sans-serif;">
                    Admin Control
                </div>
                <i class="fa-solid fa-chevron-down accordion-chevron" style="color: #d4c5b3; font-size: 0.8rem;"></i>
            </button>
            <div class="collapse {{ $isAdminActive ? 'show' : '' }}" id="collapseAdmin">
            <nav class="nav flex-column">
                @canany(['manage-users', 'manage-admins', 'manage-roles-permissions', 'manage-landing-page', 'manage-backup-restore', 'manage-rooms', 'manage-charge-codes', 'manage-credit-accounts', 'view-activity-logs', 'manage-pos-approvals'])
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line me-2"></i>
                    Dashboard
                </a>
                @endcanany

                @can('manage-users')
                <a href="{{ route('admin.users') }}"
                   class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-group me-2"></i>
                    Users
                </a>
                @endcan

                @can('manage-roles-permissions')
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
                @endcan

                @can('manage-rooms')
                <a href="{{ route('admin.rooms') }}"
                   class="nav-link {{ request()->routeIs('admin.rooms') ? 'active' : '' }}">
                    <i class="fa-solid fa-bed me-2"></i>
                    Rooms
                </a>
                @endcan

                @can('manage-landing-page')
                <a href="{{ route('admin.landing-page') }}"
                   class="nav-link {{ request()->routeIs('admin.landing-page*') ? 'active' : '' }}">
                    <i class="fa-solid fa-globe me-2"></i>
                    Landing Page
                </a>
                @endcan

                @can('manage-charge-codes')
                <a href="{{ route('admin.chargecodes') }}"
                   class="nav-link {{ request()->routeIs('admin.chargecodes') ? 'active' : '' }}">
                    <i class="fa-solid fa-receipt me-2"></i>
                    Charge Codes
                </a>
                @endcan

                @can('manage-credit-accounts')
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

                @can('view-activity-logs')
                <a href="{{ route('admin.activitylogs') }}"
                   class="nav-link {{ request()->routeIs('admin.activitylogs') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i>
                    Activity Logs
                </a>
                @endcan

                @can('manage-pos-approvals')
                <a href="{{ route('admin.pos-approvals') }}"
                   class="nav-link d-flex align-items-center justify-content-between {{ request()->routeIs('admin.pos-approvals*') ? 'active' : '' }}">
                    <span><i class="fa-solid fa-check-double me-2"></i>POS Approvals</span>
                    <span id="sidebar-pos-approvals-badge" class="badge bg-danger ms-1 d-none"></span>
                </a>
                @endcan

                @can('manage-backup-restore')
                <a href="{{ route('admin.backup-restore') }}"
                   class="nav-link {{ request()->routeIs('admin.backup-restore*') ? 'active' : '' }}">
                    <i class="fa-solid fa-hard-drive me-2"></i>
                    Backup & Restore
                </a>
                @endcan


            </nav>
            </div>
        </div>
        @endcanany

        <!-- FRONT DESK -->

            @canany(['manage-reservations', 'view-guest-list', 'view-guest-folio', 'view-shift-sales'])
            <div class="menu-section mb-4">
                @php $isFrontDeskActive = request()->routeIs('frontdesk.*'); @endphp
                <button class="btn btn-link text-decoration-none w-100 p-0 text-start d-flex justify-content-between align-items-center sidebar-accordion-btn mb-2 {{ $isFrontDeskActive ? '' : 'collapsed' }}"
                        type="button" data-bs-toggle="collapse" data-bs-target="#collapseFrontDesk" aria-expanded="{{ $isFrontDeskActive ? 'true' : 'false' }}" aria-controls="collapseFrontDesk">
                    <div class="text-uppercase small fw-bold" style="color: #d4c5b3; font-family: 'Plus Jakarta Sans', sans-serif;">
                        Front Desk
                    </div>
                    <i class="fa-solid fa-chevron-down accordion-chevron" style="color: #d4c5b3; font-size: 0.8rem;"></i>
                </button>
                <div class="collapse {{ $isFrontDeskActive ? 'show' : '' }}" id="collapseFrontDesk">
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
                   class="nav-link {{ request()->routeIs('frontdesk.guest-folio') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar me-2"></i>Guest Folio
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
        </div>
            @endcanany


        <!-- COFFEE SHOP -->

            @can('manage-inventory')
            <div class="menu-section mb-4">
            @php $isCoffeeShopActive = request()->routeIs('coffeeshop.*'); @endphp
            <button class="btn btn-link text-decoration-none w-100 p-0 text-start d-flex justify-content-between align-items-center sidebar-accordion-btn mb-2 {{ $isCoffeeShopActive ? '' : 'collapsed' }}"
                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseCoffeeShop" aria-expanded="{{ $isCoffeeShopActive ? 'true' : 'false' }}" aria-controls="collapseCoffeeShop">
                <div class="text-uppercase small fw-bold" style="color: #d4c5b3; font-family: 'Plus Jakarta Sans', sans-serif;">
                    Coffee Shop
                </div>
                <i class="fa-solid fa-chevron-down accordion-chevron" style="color: #d4c5b3; font-size: 0.8rem;"></i>
            </button>
            <div class="collapse {{ $isCoffeeShopActive ? 'show' : '' }}" id="collapseCoffeeShop">
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
        </div>
            @endcan


        <!-- ACCOUNTING -->

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
            @php $isAccountingActive = request()->routeIs('accounting.*'); @endphp
            <button class="btn btn-link text-decoration-none w-100 p-0 text-start d-flex justify-content-between align-items-center sidebar-accordion-btn mb-2 {{ $isAccountingActive ? '' : 'collapsed' }}"
                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseAccounting" aria-expanded="{{ $isAccountingActive ? 'true' : 'false' }}" aria-controls="collapseAccounting">
                <div class="text-uppercase small fw-bold" style="color: #d4c5b3; font-family: 'Plus Jakarta Sans', sans-serif;">
                    Accounting
                </div>
                <i class="fa-solid fa-chevron-down accordion-chevron" style="color: #d4c5b3; font-size: 0.8rem;"></i>
            </button>
            <div class="collapse {{ $isAccountingActive ? 'show' : '' }}" id="collapseAccounting">
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
        </div>
            @endcanany


        <!-- FOOD ORDER -->

            @can('access-foodpanda')
        <div class="menu-section mb-4">
            @php $isFoodOrderActive = request()->routeIs('admin.food-delivery'); @endphp
            <button class="btn btn-link text-decoration-none w-100 p-0 text-start d-flex justify-content-between align-items-center sidebar-accordion-btn mb-2 {{ $isFoodOrderActive ? '' : 'collapsed' }}"
                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseFoodOrder" aria-expanded="{{ $isFoodOrderActive ? 'true' : 'false' }}" aria-controls="collapseFoodOrder">
                <div class="text-uppercase small fw-bold" style="color: #d4c5b3; font-family: 'Plus Jakarta Sans', sans-serif;">
                    Food Order
                </div>
                <i class="fa-solid fa-chevron-down accordion-chevron" style="color: #d4c5b3; font-size: 0.8rem;"></i>
            </button>
            <div class="collapse {{ $isFoodOrderActive ? 'show' : '' }}" id="collapseFoodOrder">
            <nav class="nav flex-column">
                <a href="{{ route('admin.food-delivery') }}"
                   class="nav-link {{ request()->routeIs('admin.food-delivery') ? 'active' : '' }}">
                    <i class="fa-solid fa-utensils me-2"></i>
                    Food Delivery
                </a>
            </nav>
            </div>
        </div>
            @endcan


    </div>

</div>

<style>
    .menu-section + .menu-section {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 1.5rem;
        margin-top: 1.5rem;
    }
</style>