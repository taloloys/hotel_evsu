<div class="card border-0 shadow-sm mb-4">

    <div class="card-body py-3 px-4">

        <div class="d-flex justify-content-between align-items-center">

            <!-- Left Section -->

            <div class="d-flex align-items-center">

                <a href="{{ url('/') }}" class="d-flex align-items-center text-decoration-none me-3">
                    <img src="{{ asset('images/icons/icon-32x32.png') }}" alt="Larrazabal crest" style="width:36px;height:36px;object-fit:contain;border-radius:6px;">
                </a>

                <div>

                    <small class="text-muted text-uppercase fw-semibold">
                        Hotel Don Felipe
                    </small>

                    <div class="d-flex align-items-center mt-1">

                        <i class="fa-solid fa-location-dot text-primary me-2"></i>

                        <span class="fw-semibold">
                            @yield('pageTitle')
                        </span>

                    </div>

                </div>

            </div>

            <!-- Right Section -->

            <div class="d-flex align-items-center gap-3">

                <!-- Date -->

                <div class="text-end d-none d-lg-block">

                    <small class="text-muted d-block">
                        {{ now()->format('l') }}
                    </small>

                    <span class="fw-semibold">
                        {{ now()->format('F d, Y') }}
                    </span>

                </div>

                <!-- Notifications -->

                <button class="btn btn-light rounded-circle position-relative"
                        style="width:42px;height:42px;">

                    <i class="fa-solid fa-bell"></i>

                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        3
                    </span>

                </button>

                <!-- User Profile -->

                <div class="dropdown">

                    <a href="#"
                       class="d-flex align-items-center text-decoration-none"
                       data-bs-toggle="dropdown">

                        <div class="text-end me-2 d-none d-md-block">

                        </div>

                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                             style="width:42px;height:42px;">

                            <span class="text-white fw-bold">
                                DF
                            </span>

                        </div>

                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">

                        <li class="px-3 py-2">

                            <div class="fw-semibold">
                                {{ auth()->user()?->full_name ?? 'Admin User' }}
                            </div>

                            <small class="text-muted">
                                {{ auth()->user()?->role ?? 'Front Desk Staff' }}
                            </small>

                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fa-solid fa-user me-2"></i>
                                My Profile
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fa-solid fa-gear me-2"></i>
                                Account Settings
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i>
                                    Logout
                                </button>
                            </form>
                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

</div>