<div class="p-4">

    <!-- HEADER -->
    <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">

        <img src="{{ asset('images/logo.png') }}"
             alt="Don Felipe Hotel Logo"
             class="me-3"
             style="width:80px; height:70px; object-fit:contain;">

        <div>
            <h5 class="text-white fw-bold mb-0">Don Felipe Hotel</h5>
            <small class="text-secondary">Accounting Module</small>
        </div>

    </div>

    <!-- MENU TITLE -->
    <div class="text-uppercase text-secondary small fw-bold mb-3">
        Accounting
    </div>

    <nav class="nav flex-column">

        <!-- DASHBOARD -->
        <a href="{{ route('accounting.dashboard') }}"
           class="nav-link {{ request()->routeIs('accounting.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-pie me-2"></i>
            Dashboard
        </a>

        <!-- BILLING -->
        <a href="{{ route('accounting.billing') }}"
           class="nav-link {{ request()->routeIs('accounting.billing') ? 'active' : '' }}">
            <i class="fa-solid fa-file-invoice me-2"></i>
            Billing
        </a>

        <!-- PAYMENTS -->
        <a href="{{ route('accounting.payments') }}"
           class="nav-link {{ request()->routeIs('accounting.payments') ? 'active' : '' }}">
            <i class="fa-solid fa-credit-card me-2"></i>
            Payments
        </a>

        <!-- RECEIVABLES -->
        <a href="{{ route('accounting.receivables') }}"
           class="nav-link {{ request()->routeIs('accounting.receivables') ? 'active' : '' }}">
            <i class="fa-solid fa-hand-holding-dollar me-2"></i>
            Receivables
        </a>

        <!-- EXPENSES -->
        <a href="{{ route('accounting.expenses') }}"
           class="nav-link {{ request()->routeIs('accounting.expenses') ? 'active' : '' }}">
            <i class="fa-solid fa-receipt me-2"></i>
            Expenses
        </a>

        <!-- REPORTS -->
        <a href="{{ route('accounting.reports') }}"
           class="nav-link {{ request()->routeIs('accounting.reports') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-bar me-2"></i>
            Reports
        </a>

        <!-- AUDIT -->
        <a href="{{ route('accounting.audit') }}"
           class="nav-link {{ request()->routeIs('accounting.audit') ? 'active' : '' }}">
            <i class="fa-solid fa-shield-halved me-2"></i>
            Audit Logs
        </a>

    </nav>

</div>