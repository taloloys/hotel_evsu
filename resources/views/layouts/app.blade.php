<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Hotel Management System')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Favicons and PWA manifest -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icons/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/icons/icon-192x192.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <style>
        :root {
            --coffee-950: #2f1c16;
            --coffee-800: #4e342e;
            --coffee-700: #6d4c41;
            --cream: #f8f5f2;
            --latte: #efe1cf;
            --caramel: #a97142;
            --accent-green: #4caf50;
            --accent-red: #e53935;
            --border-soft: #e7dccf;
            --shadow-soft: 0 14px 34px rgba(78, 52, 46, 0.08);
        }

        body{
            background:linear-gradient(180deg, #f7efe8 0%, #f4f7fc 100%);
            font-family:'Inter', 'Segoe UI', -apple-system, sans-serif;
            font-size: 1.02rem;
            line-height: 1.5;
        }

        .sidebar{
            width:260px;
            height:100vh;
            background:linear-gradient(180deg, #2b1c15 0%, #1d120d 100%);
            position:fixed;
            left:0;
            top:0;
            overflow-y:auto;
            box-shadow: 0 0 30px rgba(0,0,0,.18);
        }

        .main-content{
            margin-left:260px;
            padding:30px;
        }

        .content-card{
            background:#fff;
            border:none;
            border-radius:20px;
            box-shadow:0 5px 20px rgba(0,0,0,.05);
        }

        .stat-card{
            border-radius:20px;
            color:white;
            padding:25px;
        }

        .nav-link{
            color:#cbd5e1;
            padding:12px 16px;
            border-radius:12px;
            margin-bottom:8px;
        }

        .nav-link:hover{
            background:#1e293b;
            color:white;
        }

        .nav-link.active{
            background:#2563eb;
            color:white;
        }

        .sidebar .menu-section {
            padding: 0.25rem 0.2rem;
        }

        .sidebar .menu-section .text-uppercase {
            letter-spacing: 0.12em;
            color: rgba(255,255,255,0.58) !important;
        }

        .sidebar .nav-link {
            color: #e9d8c9;
            padding: 0.7rem 0.8rem;
            border-radius: 0.9rem;
            margin-bottom: 0.35rem;
            transition: all 180ms ease;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.08);
            color: #ffffff;
            transform: translateX(2px);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--caramel), var(--coffee-700));
            color: #ffffff;
            box-shadow: 0 10px 20px rgba(78, 52, 46, 0.2);
        }

        .coffeeshop-nav .nav-link {
            color: #5a3c2d;
            padding: 0.85rem 1.1rem;
            border-radius: 0.85rem;
            margin-bottom: 0.35rem;
            border: 1px solid transparent;
            background: rgba(255, 255, 255, 0.72);
            font-weight: 600;
            font-size: 1.02rem;
            transition: all 180ms ease;
        }

        .coffeeshop-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.92);
            color: var(--coffee-950);
            border-color: rgba(111, 78, 56, 0.16);
            transform: translateX(2px);
        }

        .coffeeshop-nav .nav-link.active {
            background: linear-gradient(135deg, var(--coffee-700), var(--caramel));
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 8px 16px rgba(78, 52, 46, 0.16);
        }

        .coffeeshop-page-shell {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .coffeeshop-hero {
            background: linear-gradient(135deg, var(--coffee-800) 0%, #6a4338 100%);
            color: white;
            border-radius: 1.25rem;
            padding: 1.5rem 1.6rem;
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            position: relative;
        }

        .coffeeshop-hero::after {
            content: '';
            position: absolute;
            inset: auto -30px -60px auto;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
        }

        .coffeeshop-panel {
            background: rgba(255,255,255,0.95);
            border: 1px solid var(--border-soft);
            border-radius: 1.15rem;
            box-shadow: var(--shadow-soft);
            overflow: hidden;
        }

        .coffeeshop-card {
            border: 1px solid var(--border-soft);
            border-radius: 1rem;
            background: linear-gradient(180deg, #fffdfb 0%, #fcf7f0 100%);
            box-shadow: 0 8px 22px rgba(78, 52, 46, 0.06);
            overflow: hidden;
        }

        .coffeeshop-table thead th {
            background-color: #f6ebdc !important;
            color: #6b4d3b;
            font-size: 0.85rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            font-weight: 700;
            padding-top: 0.85rem;
            padding-bottom: 0.85rem;
        }

        .coffeeshop-table td {
            padding-top: 0.95rem;
            padding-bottom: 0.95rem;
            font-size: 1.02rem;
        }

        .coffeeshop-table tbody tr:hover {
            background-color: #fcf5ea;
        }

        .coffeeshop-pill {
            border-radius: 999px;
            padding: 0.45rem 0.82rem;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .coffeeshop-nav-pills .nav-link {
            background-color: #f4ebdf;
            color: #6b4d3b;
            border: 1px solid #e6d9c5;
            font-weight: 600;
            transition: all 180ms ease;
            border-radius: 999px;
        }

        .coffeeshop-nav-pills .nav-link:hover {
            background-color: #efe0c5;
            color: var(--coffee-800);
        }

        .coffeeshop-nav-pills .nav-link.active {
            background: linear-gradient(135deg, var(--coffee-800), #6b4338);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 8px 20px rgba(78, 52, 46, 0.2);
        }

        .coffeeshop-form-control {
            border-color: #e1d2c0;
            border-radius: 0.85rem;
        }

        .coffeeshop-form-control:focus {
            border-color: var(--caramel);
            box-shadow: 0 0 0 0.2rem rgba(169, 113, 66, 0.18);
        }

        #tab-switcher {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            grid-auto-rows: minmax(3.6rem, auto);
            gap: 0.6rem;
            align-items: stretch;
            max-height: 12.5rem;
            overflow-y: auto;
            padding-right: 0.2rem;
            scrollbar-width: thin;
            scrollbar-color: var(--caramel) rgba(78, 52, 46, 0.12);
        }

        #tab-switcher::-webkit-scrollbar {
            width: 0.45rem;
        }

        #tab-switcher::-webkit-scrollbar-track {
            background: rgba(78, 52, 46, 0.08);
            border-radius: 999px;
        }

        #tab-switcher::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--caramel), var(--coffee-700));
            border-radius: 999px;
        }

        #tab-switcher::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #b57a4a, var(--coffee-800));
        }

        #tab-switcher button {
            width: 100%;
            height: 100%;
            min-height: 3.6rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            padding: 0.75rem 0.85rem;
            line-height: 1.2;
            text-align: center;
        }

        #tab-switcher button.btn-primary {
            font-size: 1rem;
            font-weight: 600;
        }

        #tab-switcher button.btn-outline-secondary {
            font-size: 0.95rem;
        }

        #active-tab-name {
            font-size: 1.1rem;
        }

        .coffeeshop-card .form-control,
        .coffeeshop-card .form-select,
        .coffeeshop-card .input-group,
        .coffeeshop-card .btn {
            border-radius: 0.85rem;
        }

        .coffeeshop-card .input-group {
            overflow: hidden;
        }

        #product-grid {
            align-items: stretch;
        }

        #product-grid .product-tile {
            display: flex;
        }

        #product-grid .product-tile .coffeeshop-card {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        #product-grid .product-tile .coffeeshop-card .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1rem;
        }

        .product-tile .coffeeshop-card .fw-semibold {
            font-size: 0.95rem;
            margin-bottom: 0.35rem;
        }

        #product-grid .product-tile .coffeeshop-card small,
        #cart-items small {
            font-size: 0.82rem;
        }

        #cart-items {
            max-height: calc(100vh - 420px);
            overflow-y: auto;
            overflow-x: hidden;
            min-height: 10rem;
            padding-right: 0.35rem;
            width: 100%;
            min-width: 0;
        }

        #cart-items .cart-row {
            display: grid;
            grid-template-columns: 1fr auto;
            column-gap: 0.75rem;
            align-items: center;
            gap: 0.75rem;
        }

        #cart-items .cart-row .item-details {
            min-width: 0;
        }

        #cart-items .remove-item-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        #cart-items .remove-item-btn i {
            line-height: 1;
        }

        #cart-items .fw-semibold {
            font-size: 0.95rem;
        }

        .coffeeshop-card .card-body {
            padding: 1.15rem;
        }

        .checkout-action-btn {
            font-size: 1rem;
            padding: 0.85rem 1rem;
            border-radius: 0.95rem;
        }

        .checkout-action-btn.btn-outline-secondary,
        .checkout-action-btn.btn-outline-info,
        .checkout-action-btn.btn-outline-danger {
            font-weight: 600;
        }

        /* =========================================================
           FRONTDESK UNIFIED DESIGN SYSTEM
           ========================================================= */
        .badge-status {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.65rem;
            font-size: 0.76rem;
            font-weight: 600;
            border-radius: 6px;
            letter-spacing: 0.03em;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .badge-status-checkedin { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .badge-status-reserved { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-status-open { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-status-available { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-status-cleaning { background-color: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
        .badge-status-maintenance { background-color: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
        .badge-status-occupied { background-color: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
        .badge-status-closed { background-color: #e5e7eb; color: #1f2937; border: 1px solid #d1d5db; }
        .badge-status-active { background-color: #ccfbf1; color: #115e59; border: 1px solid #99f6e4; }

        .fd-toolbar {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .fd-search {
            height: 45px;
            width: 320px;
            border: 1px solid #000;
            border-radius: 6px;
            overflow: hidden;
            background: #fff;
        }

        .fd-search .input-group-text,
        .fd-search .form-control {
            height: 100%;
            border: 0;
            box-shadow: none;
            background: #fff;
        }

        .fd-select {
            height: 45px;
            border: 1px solid #000 !important;
            border-radius: 6px;
            box-shadow: none !important;
            font-size: 0.95rem;
        }

        .fd-filter-btn {
            height: 45px;
            border-radius: 6px;
            border: 1px solid #000;
            font-size: 0.95rem;
        }

        .fd-static-field {
            height: 45px;
            display: flex;
            align-items: center;
            padding: 0 1rem;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            color: #334155;
            font-weight: 500;
        }

        .fd-empty-state {
            padding: 2.5rem 1rem;
            text-align: center;
            color: #64748b;
        }

        .fd-empty-state i {
            font-size: 2rem;
            margin-bottom: 0.75rem;
            opacity: 0.5;
        }
    </style>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Hotwire Turbo -->
    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.5/dist/turbo.es2017-umd.js" defer></script>
    <script>
        function updateSidebarBadges(data) {
            const badgeMap = {
                'sidebar-low-stock-badge': data.lowStockCount,
                'sidebar-pending-checkins-badge': data.pendingCheckinsCount,
                'sidebar-pending-checkouts-badge': data.pendingCheckoutsCount,
                'sidebar-pending-expenses-badge': data.pendingExpensesCount,
                'sidebar-pos-approvals-badge': data.posApprovalsCount
            };

            for (const [id, count] of Object.entries(badgeMap)) {
                const el = document.getElementById(id);
                if (el) {
                    if (count && count > 0) {
                        el.textContent = count;
                        el.classList.remove('d-none');
                    } else {
                        el.classList.add('d-none');
                    }
                }
            }
        }

        function fetchLayoutData() {
            fetch('{{ route('api.layout-data') }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (window.initNotifications) {
                    window.initNotifications(data.notifications);
                }
                updateSidebarBadges(data);
            })
            .catch(err => console.error('Error fetching layout data:', err));
        }

        // Restore sidebar scroll position from sessionStorage
        function restoreSidebarScroll() {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                const scrollTop = sessionStorage.getItem('sidebar-scroll');
                if (scrollTop !== null) {
                    sidebar.scrollTop = parseInt(scrollTop, 10);
                }
            }
        }

        // Listen for scroll events on the sidebar using capturing event listener at document level
        document.addEventListener('scroll', function(event) {
            const target = event.target;
            if (target && target.classList && target.classList.contains('sidebar')) {
                sessionStorage.setItem('sidebar-scroll', target.scrollTop);
            }
        }, true);

        // Clear sidebar scroll position on logout form submit
        document.addEventListener('submit', function(event) {
            const action = event.target.getAttribute('action');
            if (action && action.includes('logout')) {
                sessionStorage.removeItem('sidebar-scroll');
            }
        });

        document.addEventListener('DOMContentLoaded', fetchLayoutData);
        document.addEventListener('turbo:load', function() {
            restoreSidebarScroll();
            fetchLayoutData();
            if (window.layoutDataInterval) {
                clearInterval(window.layoutDataInterval);
            }
            window.layoutDataInterval = setInterval(fetchLayoutData, 10000); // 10 seconds
        });

        // Clean up Bootstrap modals and backdrops before caching the page
        document.addEventListener('turbo:before-cache', function() {
            const openModals = document.querySelectorAll('.modal.show');
            openModals.forEach(modalEl => {
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                } else {
                    modalEl.classList.remove('show');
                    modalEl.style.display = 'none';
                }
            });
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });

        // Ensure no stray backdrops or modal-open classes are carried over to the new body
        document.addEventListener('turbo:before-render', function(event) {
            const newSidebar = event.detail.newBody.querySelector('.sidebar');
            if (newSidebar) {
                const scrollTop = sessionStorage.getItem('sidebar-scroll');
                if (scrollTop !== null) {
                    newSidebar.scrollTop = parseInt(scrollTop, 10);
                }
            }

            event.detail.newBody.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            event.detail.newBody.classList.remove('modal-open');
            event.detail.newBody.style.removeProperty('overflow');
            event.detail.newBody.style.removeProperty('padding-right');
        });

        // Restore scroll position after new body is rendered
        document.addEventListener('turbo:render', restoreSidebarScroll);
    </script>

    @stack('styles')
</head>

<body
    data-flash-success="{{ session('success') }}"
    data-flash-error="{{ session('error') }}"
    data-flash-validation="{{ $errors->any() ? $errors->first() : '' }}"
    data-login-confirmation="{{ session('show_login_confirmation') ? 'true' : '' }}"
    data-login-username="{{ Auth::user()?->username ?? '' }}"
    data-login-role="{{ Auth::user()?->role?->role_name ?? '' }}"
>

<div class="sidebar">
    @include('layouts.sidebar')
</div>

<div class="main-content">

    @include('layouts.topbar')

    <div class="mt-4">
        @yield('content')
    </div>

</div>

@stack('modals')

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('turbo:load', function() {
        const body = document.body;

        // Read and immediately clear so Turbo cache replays never re-fire
        const flashSuccess    = body.getAttribute('data-flash-success');
        const flashError      = body.getAttribute('data-flash-error');
        const flashValidation = body.getAttribute('data-flash-validation');

        body.removeAttribute('data-flash-success');
        body.removeAttribute('data-flash-error');
        body.removeAttribute('data-flash-validation');

        if (flashSuccess) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: flashSuccess,
                confirmButtonColor: '#2563eb',
                timer: 3000,
                timerProgressBar: true
            });
        } else if (flashError) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: flashError,
                confirmButtonColor: '#2563eb'
            });
        } else if (flashValidation) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: flashValidation,
                confirmButtonColor: '#2563eb'
            });
        }

        const loginConfirmation = body.getAttribute('data-login-confirmation') === 'true';
        const loginUsername = body.getAttribute('data-login-username');
        const loginRole = body.getAttribute('data-login-role');

        body.removeAttribute('data-login-confirmation');
        body.removeAttribute('data-login-username');
        body.removeAttribute('data-login-role');

        if (loginConfirmation && loginUsername) {
            Swal.fire({
                icon: 'question',
                title: 'Confirm Account',
                html: `You are signed in as <strong>${loginUsername}</strong><br>Role: <strong>${loginRole || 'Unknown'}</strong>`,
                showCancelButton: true,
                confirmButtonText: 'Proceed',
                cancelButtonText: 'No, go back',
                confirmButtonColor: '#a97142',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-[1.5rem]',
                    title: 'text-xl font-semibold',
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    const element = document.documentElement;
                    const requestFullscreen = element.requestFullscreen || element.webkitRequestFullscreen || element.mozRequestFullScreen || element.msRequestFullscreen;
                    if (requestFullscreen) {
                        requestFullscreen.call(element).catch(() => {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Unable to fullscreen',
                                text: 'Your browser blocked fullscreen mode. Please press F11 manually.',
                                confirmButtonColor: '#a97142'
                            });
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Fullscreen not supported',
                            text: 'Your browser does not support fullscreen mode.',
                            confirmButtonColor: '#a97142'
                        });
                    }
                } else {
                    window.location.href = '{{ route('login') }}';
                }
            });
        }
    });

    // Global Anti-Duplication & Button Loading Helper
    window.setBtnLoading = function(btn, isLoading, loadingText) {
        if (!btn) return;
        if (isLoading) {
            if (!btn.dataset.originalHtml) {
                btn.dataset.originalHtml = btn.innerHTML;
            }
            btn.disabled = true;
            const spinner = '<i class="fa-solid fa-spinner fa-spin me-1"></i> ';
            btn.innerHTML = spinner + (loadingText || 'Processing...');
        } else {
            if (btn.dataset.originalHtml !== undefined) {
                btn.innerHTML = btn.dataset.originalHtml;
                delete btn.dataset.originalHtml;
            }
            btn.disabled = false;
        }
    };

    // Global Anti-Duplication Form Submit Listener
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || form.getAttribute('data-no-loading') === 'true') return;

        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]')
                       || form.querySelector('button:not([type="button"])');
        if (submitBtn && !submitBtn.disabled) {
            if (form.dataset.submitting === 'true') {
                e.preventDefault();
                return false;
            }
            form.dataset.submitting = 'true';

            let text = submitBtn.innerText.trim() || 'Processing...';
            window.setBtnLoading(submitBtn, true, text);
        }
    }, true);

    // Global Fullscreen Toggle Function & Helper
    window.toggleAppFullscreen = function() {
        const elem = document.documentElement;
        if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.mozFullScreenElement && !document.msFullscreenElement) {
            const reqFS = elem.requestFullscreen || elem.webkitRequestFullscreen || elem.mozRequestFullScreen || elem.msRequestFullscreen;
            if (reqFS) {
                reqFS.call(elem).then(window.updateFullscreenIcon).catch(() => {});
            }
        } else {
            const exitFS = document.exitFullscreen || document.webkitExitFullscreen || document.mozCancelFullScreen || document.msExitFullscreen;
            if (exitFS) {
                exitFS.call(document).then(window.updateFullscreenIcon).catch(() => {});
            }
        }
    };

    window.updateFullscreenIcon = function() {
        const icon = document.getElementById('fullscreenIcon');
        if (icon) {
            const isFS = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
            if (isFS) {
                icon.className = 'fa-solid fa-compress';
            } else {
                icon.className = 'fa-solid fa-expand';
            }
        }
    };

    // Cross-Browser Fullscreen Change Event Listeners
    ['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'MSFullscreenChange'].forEach(eventName => {
        document.addEventListener(eventName, function() {
            window.updateFullscreenIcon();
            const isFS = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
            sessionStorage.setItem('appWasFullscreen', isFS ? 'true' : 'false');
        });
    });

    document.addEventListener('turbo:load', function() {
        window.updateFullscreenIcon();
    });

    if (sessionStorage.getItem('appWasFullscreen') === 'true') {
        const restoreFS = function() {
            if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                const elem = document.documentElement;
                const reqFS = elem.requestFullscreen || elem.webkitRequestFullscreen || elem.mozRequestFullScreen || elem.msRequestFullscreen;
                if (reqFS) {
                    reqFS.call(elem).then(window.updateFullscreenIcon).catch(() => {});
                }
            }
        };
        restoreFS();
        document.addEventListener('click', restoreFS, { once: true });
    }
</script>



@stack('scripts')

</body>
</html>