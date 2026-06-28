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
        body{
            background:#f4f7fc;
            font-family:'Segoe UI',sans-serif;
        }

        .sidebar{
            width:260px;
            height:100vh;
            background:#0f172a;
            position:fixed;
            left:0;
            top:0;
            overflow-y:auto;
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
    </style>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Hotwire Turbo -->
    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.5/dist/turbo.es2017-umd.js" defer></script>
    <script>
        document.addEventListener('turbo:load', function() {
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

<body>

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
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: {!! json_encode(session('success')) !!},
                confirmButtonColor: '#2563eb',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: {!! json_encode(session('error')) !!},
                confirmButtonColor: '#2563eb'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: {!! json_encode($errors->first()) !!},
                confirmButtonColor: '#2563eb'
            });
        @endif
    });
</script>



@stack('scripts')

</body>
</html>