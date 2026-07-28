@php
    $user = auth()->user();
    $initials = 'DF'; // Default fallback

    if ($user && $user->full_name) {
        $words = explode(' ', preg_replace('/\s+/', ' ', trim($user->full_name)));
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[count($words) - 1], 0, 1));
        } else if (count($words) == 1 && strlen($words[0]) > 0) {
            $name = $words[0];
            preg_match_all('/[A-Z]/', $name, $matches);
            if (isset($matches[0]) && count($matches[0]) >= 2) {
                $initials = implode('', array_slice($matches[0], 0, 2));
            } else {
                $initials = strtoupper(substr($name, 0, min(2, strlen($name))));
            }
        }
    }
@endphp

<div class="card border-0 shadow-sm mb-4 position-relative" style="z-index: 1030;">

    <div class="card-body py-3 px-4">

        <div class="d-flex justify-content-between align-items-center">


            <div class="d-flex align-items-center">

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
                        {{ now()->format('F d, Y') }} <span id="header-time" class="ms-1 text-primary">{{ now()->format('h:i:s A') }}</span>
                    </span>

                </div>

                <!-- Fullscreen Toggle -->
                <button class="btn btn-light rounded-circle me-2"
                        type="button"
                        id="fullscreenToggleBtn"
                        title="Toggle Fullscreen"
                        onclick="toggleAppFullscreen()"
                        style="width:42px;height:42px;">
                    <i class="fa-solid fa-expand" id="fullscreenIcon"></i>
                </button>

                <!-- Notifications -->

                <div class="dropdown">

                    <button class="btn btn-light rounded-circle position-relative"
                            type="button"
                            id="notificationDropdown"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            style="width:42px;height:42px;">

                        <i class="fa-solid fa-bell"></i>

                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notificationBadge">
                            0
                        </span>

                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 py-0"
                        aria-labelledby="notificationDropdown"
                        style="width: 320px; z-index: 1050;">

                        <li class="p-3 border-bottom bg-light">
                            <span class="fw-bold">Notifications</span>
                        </li>

                        <div id="notificationList" style="max-height: 400px; overflow-y: auto;">
                            <!-- Dynamic items will be rendered here -->
                        </div>

                    </ul>

                </div>

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
                                {{ $initials }}
                            </span>

                        </div>

                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">

                        <li class="px-3 py-2">

                            <div class="fw-semibold">
                                {{ auth()->user()?->full_name ?? 'Admin User' }}
                            </div>

                            <small class="text-muted">
                                {{ auth()->user()?->role?->role_name ?? 'Front Desk Staff' }}
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
                            <form method="POST" action="{{ route('logout') }}" data-turbo="false">
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

@push('styles')
<style>
    /* Styling for premium notifications */
    #notificationDropdown::after {
        display: none !important;
    }
    .notification-item {
        transition: background-color 0.2s ease-in-out;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    .notification-item:hover {
        background-color: #f8fafc !important;
    }
    .notification-item .dismiss-btn {
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
    }
    .notification-item:hover .dismiss-btn {
        opacity: 1;
    }
</style>
@endpush

@push('scripts')
<script>
    // Global function to initialize notifications when layout data is loaded
    window.initNotifications = function(notificationsData) {
        const notificationList = document.getElementById('notificationList');
        const notificationBadge = document.getElementById('notificationBadge');
        const clearAllBtn = document.getElementById('clearAllNotifications');

        if (!notificationList || !notificationBadge) {
            return;
        }

        function getDismissed() {
            return [];
        }

        function setDismissed(ids) {
            localStorage.removeItem('dismissed_notifications');
        }

        function renderNotifications() {
            const active = notificationsData;

            if (active.length > 0) {
                notificationBadge.textContent = active.length;
                notificationBadge.classList.remove('d-none');
                
                notificationList.innerHTML = '';
                active.forEach(n => {
                    const item = document.createElement('li');
                    item.className = 'dropdown-item p-3 d-flex align-items-start gap-3 position-relative notification-item';
                    item.style.whiteSpace = 'normal';
                    item.style.cursor = 'pointer';
                    
                    item.innerHTML = `
                        <div class="mt-1 flex-shrink-0">
                            <i class="fa-solid ${n.icon} fa-fw fs-5"></i>
                        </div>
                        <div class="flex-grow-1 notif-body">
                            <div class="small text-dark mb-1">${n.message}</div>
                            <small class="text-muted text-xs">${n.time}</small>
                        </div>
                    `;
                    
                    item.addEventListener('click', function(e) {
                        if (window.Turbo) {
                            window.Turbo.visit(n.link);
                        } else {
                            window.location.href = n.link;
                        }
                    });
                    
                    notificationList.appendChild(item);
                });
            } else {
                notificationBadge.classList.add('d-none');
                notificationList.innerHTML = `
                    <li class="p-4 text-center text-muted">
                        <i class="fa-solid fa-bell-slash d-block fs-3 mb-2 opacity-50"></i>
                        <span class="small">No new notifications</span>
                    </li>
                `;
            }
        }

        function dismissNotification(id) {
            const dismissed = getDismissed();
            if (!dismissed.includes(id)) {
                dismissed.push(id);
                setDismissed(dismissed);
                renderNotifications();
            }
        }
        window.dismissNotification = dismissNotification;

        if (clearAllBtn) {
            // Remove previous event listeners to avoid double bindings on layout updates
            const newClearAllBtn = clearAllBtn.cloneNode(true);
            clearAllBtn.parentNode.replaceChild(newClearAllBtn, clearAllBtn);
            newClearAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const dismissed = getDismissed();
                notificationsData.forEach(n => {
                    if (!dismissed.includes(n.id)) {
                        dismissed.push(n.id);
                    }
                });
                setDismissed(dismissed);
                renderNotifications();
            });
        }

        renderNotifications();
    };

    // Ticking clock initializer
    (function() {
        const timeElement = document.getElementById('header-time');
        if (timeElement) {
            // Get initial server time in milliseconds
            let serverTimeMs = {{ now()->getTimestamp() * 1000 }};
            const startTimePerformance = performance.now();

            function updateClock() {
                // Calculate elapsed time using high-resolution timer to avoid setInterval drift
                const elapsed = performance.now() - startTimePerformance;
                const currentServerTime = new Date(serverTimeMs + elapsed);

                let hours = currentServerTime.getHours();
                const minutes = String(currentServerTime.getMinutes()).padStart(2, '0');
                const seconds = String(currentServerTime.getSeconds()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12;
                const hoursStr = String(hours).padStart(2, '0');
                timeElement.textContent = hoursStr + ':' + minutes + ':' + seconds + ' ' + ampm;

                // Expose current server time globally so other components (like checkout modal) can read it
                window.currentServerTime = currentServerTime;
            }
            updateClock();
            if (window.headerClockInterval) {
                clearInterval(window.headerClockInterval);
            }
            window.headerClockInterval = setInterval(updateClock, 1000);
        }
    })();
</script>
@endpush