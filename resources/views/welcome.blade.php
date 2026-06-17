<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Don Felipe Hotel Management System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>

        body{
            background:#f5f7fb;
            font-family:'Segoe UI',sans-serif;
        }

        .hero{
            min-height:100vh;
            background:
            linear-gradient(rgba(0,0,0,.55),
            rgba(0,0,0,.55)),
            url('https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=2070')
            center/cover no-repeat;
        }

        .hero-content{
            color:white;
        }

        .feature-card{
            border:none;
            border-radius:20px;
            transition:.3s;
        }

        .feature-card:hover{
            transform:translateY(-5px);
        }

        .system-btn{
            padding:12px 30px;
            border-radius:12px;
            font-weight:600;
        }

        .section-title{
            font-weight:700;
        }

        .footer{
            background:#0f172a;
            color:white;
        }

    </style>

</head>
<body>

    <!-- Hero Section -->

    <section class="hero d-flex align-items-center">

        <div class="container">

            <div class="row align-items-center">

                <div class="col-lg-7">

                    <div class="hero-content">

                        <span class="badge bg-primary mb-3">
                            HOTEL MANAGEMENT SYSTEM
                        </span>

                        <h1 class="display-4 fw-bold mb-3">
                            Don Felipe Hotel
                        </h1>

                        <p class="lead mb-4">
                            Streamline reservations, registrations,
                            guest folios, sales reconciliation,
                            and guest management in one centralized platform.
                        </p>

                        <a href="{{ route('dashboard') }}"
                           class="btn btn-primary system-btn me-2">

                            <i class="fa-solid fa-gauge-high me-2"></i>
                            Enter Dashboard

                        </a>

                        <a href="#features"
                           class="btn btn-outline-light system-btn">

                            Learn More

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- Features -->

    <section id="features" class="py-5">

        <div class="container">

            <div class="text-center mb-5">

                <h2 class="section-title">
                    System Modules
                </h2>

                <p class="text-muted">
                    Complete hotel operation management tools.
                </p>

            </div>

            <div class="row g-4">

                <div class="col-md-4">

                    <div class="card feature-card shadow-sm h-100">

                        <div class="card-body text-center p-4">

                            <i class="fa-solid fa-bed fa-3x text-primary mb-3"></i>

                            <h5>Reservation</h5>

                            <p class="text-muted">
                                Manage room bookings, arrivals,
                                departures and availability.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="card feature-card shadow-sm h-100">

                        <div class="card-body text-center p-4">

                            <i class="fa-solid fa-user-plus fa-3x text-success mb-3"></i>

                            <h5>Registration</h5>

                            <p class="text-muted">
                                Fast guest check-in and room assignment.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="card feature-card shadow-sm h-100">

                        <div class="card-body text-center p-4">

                            <i class="fa-solid fa-file-invoice-dollar fa-3x text-warning mb-3"></i>

                            <h5>Guest Folio</h5>

                            <p class="text-muted">
                                Track charges, payments and balances.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="card feature-card shadow-sm h-100">

                        <div class="card-body text-center p-4">

                            <i class="fa-solid fa-cash-register fa-3x text-danger mb-3"></i>

                            <h5>Shift Sales</h5>

                            <p class="text-muted">
                                Reconcile cashier shifts and monitor sales.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="card feature-card shadow-sm h-100">

                        <div class="card-body text-center p-4">

                            <i class="fa-solid fa-users fa-3x text-info mb-3"></i>

                            <h5>Guest List</h5>

                            <p class="text-muted">
                                Monitor in-house and upcoming guests.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- Statistics -->

    <section class="py-5 bg-white">

        <div class="container">

            <div class="row text-center">

                <div class="col-md-3">

                    <h2 class="fw-bold text-primary">
                        250+
                    </h2>

                    <p class="text-muted">
                        Reservations
                    </p>

                </div>

                <div class="col-md-3">

                    <h2 class="fw-bold text-success">
                        120+
                    </h2>

                    <p class="text-muted">
                        Active Guests
                    </p>

                </div>

                <div class="col-md-3">

                    <h2 class="fw-bold text-warning">
                        95%
                    </h2>

                    <p class="text-muted">
                        Occupancy Rate
                    </p>

                </div>

                <div class="col-md-3">

                    <h2 class="fw-bold text-danger">
                        ₱500K+
                    </h2>

                    <p class="text-muted">
                        Monthly Revenue
                    </p>

                </div>

            </div>

        </div>

    </section>

    <!-- Footer -->

    <footer class="footer py-4">

        <div class="container text-center">

            <h5 class="mb-2">
                Don Felipe Hotel Management System
            </h5>

            <p class="mb-0 text-light">
                © {{ date('Y') }} All Rights Reserved
            </p>

        </div>

    </footer>

</body>
</html>