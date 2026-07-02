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
            font-family:'Segoe UI',sans-serif;
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
            padding: 0.8rem 1rem;
            border-radius: 0.85rem;
            margin-bottom: 0.35rem;
            border: 1px solid transparent;
            background: rgba(255, 255, 255, 0.72);
            font-weight: 600;
            font-size: 0.96rem;
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
            gap: 1rem;
        }

        .coffeeshop-hero {
            background: linear-gradient(135deg, var(--coffee-800) 0%, #6a4338 100%);
            color: white;
            border-radius: 1.2rem;
            padding: 1.2rem 1.3rem;
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
            border-radius: 1.1rem;
            box-shadow: var(--shadow-soft);
            overflow: hidden;
        }

        .coffeeshop-card {
            border: 1px solid var(--border-soft);
            border-radius: 1rem;
            background: linear-gradient(180deg, #fffdfb 0%, #fcf7f0 100%);
            box-shadow: 0 8px 22px rgba(78, 52, 46, 0.06);
        }

        .coffeeshop-table thead th {
            background-color: #f6ebdc !important;
            color: #6b4d3b;
            font-size: 0.78rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            font-weight: 700;
        }

        .coffeeshop-table tbody tr:hover {
            background-color: #fcf5ea;
        }

        .coffeeshop-pill {
            border-radius: 999px;
            padding: 0.42rem 0.75rem;
            font-size: 0.76rem;
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
    </style>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Hotwire Turbo -->
    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.5/dist/turbo.es2017-umd.js" defer></script>
    <script>
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
                
                const lowStockBadge = document.getElementById('sidebar-low-stock-badge');
                if (lowStockBadge) {
                    if (data.lowStockCount > 0) {
                        lowStockBadge.textContent = data.lowStockCount;
                        lowStockBadge.classList.remove('d-none');
                    } else {
                        lowStockBadge.classList.add('d-none');
                    }
                }
            })
            .catch(err => console.error('Error fetching layout data:', err));
        }

        document.addEventListener('turbo:load', function() {
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
            event.detail.newBody.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            event.detail.newBody.classList.remove('modal-open');
            event.detail.newBody.style.removeProperty('overflow');
            event.detail.newBody.style.removeProperty('padding-right');
        });
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
</script>



@stack('scripts')

</body>
</html>