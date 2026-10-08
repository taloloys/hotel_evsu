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

<div class="card border-0 shadow-sm mb-4 position-relative" style="z-index: 1030; background: #f8f3ed; border-bottom: 1px solid rgba(130, 117, 103, 0.25) !important;">


    <div class="card-body py-3 px-4">

        <div class="d-flex justify-content-between align-items-center">


            <div class="d-flex align-items-center">

                <button class="btn btn-light d-lg-none me-2 shadow-sm rounded-circle d-flex align-items-center justify-content-center"
                        type="button"
                        id="sidebarToggleBtn"
                        title="Toggle Navigation Menu"
                        onclick="window.toggleAppSidebar()"
                        style="width:40px;height:40px; background: #ffffff; border: 1px solid rgba(130,117,103,0.3); color: #334c42;">
                    <i class="fa-solid fa-bars fs-5"></i>
                </button>

                <div>

                    <small class="text-uppercase fw-bold" style="color: #3d3329; font-family: 'Plus Jakarta Sans', sans-serif;">
                        EVSU
                    </small>

                    <div class="d-flex align-items-center mt-1">

                        <i class="fa-solid fa-location-dot me-2" style="color: #3a594b;"></i>

                        <span class="fw-bold font-display text-truncate" style="color: #29211a; font-size: 1.1rem; max-width: 220px;">
                            @yield('pageTitle')
                        </span>

                    </div>

                </div>

            </div>

            <!-- Right Section -->

            <div class="d-flex align-items-center gap-2 gap-sm-3">

                <!-- Date -->

                <div class="text-end d-none d-lg-block">

                    <small class="d-block fw-semibold" style="color: #3d3329; font-family: 'Plus Jakarta Sans', sans-serif;">
                        {{ now()->format('l') }}
                    </small>

                    <span class="fw-bold" style="color: #29211a; font-family: 'Plus Jakarta Sans', sans-serif;">
                        {{ now()->format('F d, Y') }} <span id="header-time" class="ms-1" style="color: #3a594b;">{{ now()->format('h:i:s A') }}</span>
                    </span>

                </div>

                <!-- Fullscreen Toggle -->
                <button class="btn btn-light rounded-circle me-1"
                        type="button"
                        id="fullscreenToggleBtn"
                        title="Toggle Fullscreen"
                        onclick="toggleAppFullscreen()"
                        style="width:42px;height:42px; background: #ffffff; border: 1px solid rgba(130,117,103,0.3); color: #504538;">
                    <i class="fa-solid fa-expand" id="fullscreenIcon"></i>
                </button>


                <!-- Notifications -->

                <div class="dropdown">

                    <button class="btn position-relative notif-bell-btn"
                            type="button"
                            id="notificationDropdown"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            style="width:42px;height:42px;background:#334c42;border-radius:50%;box-shadow:0 4px 14px rgba(51,76,66,0.3);border:none;">

                        <i class="fa-solid fa-bell notif-bell text-white" id="notificationBellIcon"></i>

                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notif-badge d-none" id="notificationBadge">
                            0
                        </span>

                    </button>

                    <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 p-0"
                        aria-labelledby="notificationDropdown"
                        style="width: 380px; max-width: calc(100vw - 24px); z-index: 1050; overflow: hidden;">

                        <div class="p-3 border-bottom bg-light d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold font-display" style="color: #504538; font-size: 1rem;">Notifications</span>
                                <span id="notificationUnreadPill" class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle d-none" style="font-size: 0.72rem;">0 unread</span>
                            </div>
                            <button type="button" 
                                    id="markAllReadBtn" 
                                    class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold d-none" 
                                    style="color: #334c42; font-size: 0.78rem;"
                                    title="Mark all notifications as read">
                                <i class="fa-solid fa-check-double me-1"></i>Mark all as read
                            </button>
                        </div>

                        <div id="notificationList" class="custom-scrollbar" style="max-height: 380px; overflow-y: auto; overscroll-behavior: contain;">
                            <!-- Dynamic items will be rendered here -->
                        </div>

                    </div>

                </div>

                <!-- User Profile -->

                <div class="dropdown">

                    <a href="#"
                       class="d-flex align-items-center text-decoration-none"
                       data-bs-toggle="dropdown">

                        <div class="text-end me-2 d-none d-md-block">

                        </div>

                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:42px;height:42px; background: #334c42 !important;">

                            <span class="text-white fw-bold font-display">
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
                            <a class="dropdown-item" href="{{ route('profile.show') }}">
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

<style>
    /* ── Notification bell button glow ── */
    .notif-bell-btn {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .notif-bell-btn:hover {
        transform: scale(1.08);
        box-shadow: 0 6px 20px rgba(245, 158, 11, 0.7) !important;
    }
    .notif-bell-btn:active {
        transform: scale(0.95);
    }

    /* ── Notification dropdown arrow hidden ── */
    #notificationDropdown::after {
        display: none !important;
    }

    /* ── Custom Scrollbar for Notification List ── */
    #notificationList::-webkit-scrollbar {
        width: 6px;
    }
    #notificationList::-webkit-scrollbar-track {
        background: #f8f6f2;
    }
    #notificationList::-webkit-scrollbar-thumb {
        background: #cfc6b8;
        border-radius: 4px;
    }
    #notificationList::-webkit-scrollbar-thumb:hover {
        background: #9b8e7e;
    }

    /* ── Notification item styles ── */
    .notification-item {
        position: relative;
        transition: background-color 0.15s ease, opacity 0.15s ease;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    }
    .notification-item.is-unread {
        background-color: #ffffff;
    }
    .notification-item.is-unread::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background-color: #334c42;
        border-top-left-radius: 2px;
        border-bottom-left-radius: 2px;
    }
    .notification-item.is-read {
        background-color: #fbfaf8;
        opacity: 0.85;
    }
    .notification-item:hover {
        background-color: #f3efe9 !important;
        opacity: 1;
    }
    .notification-item:last-child {
        border-bottom: none;
    }

    /* Mark as read icon button */
    .btn-mark-item-read {
        opacity: 0.6;
        transition: opacity 0.15s ease, transform 0.15s ease, color 0.15s ease;
        color: #7a6e60;
    }
    .btn-mark-item-read:hover {
        opacity: 1;
        transform: scale(1.15);
        color: #334c42;
    }

    /* ── Continuous bell ring swing ── */
    @keyframes bell-ring {
        0%   { transform: rotate(0deg); }
        5%   { transform: rotate(18deg); }
        10%  { transform: rotate(-16deg); }
        15%  { transform: rotate(14deg); }
        20%  { transform: rotate(-12deg); }
        25%  { transform: rotate(9deg); }
        30%  { transform: rotate(-6deg); }
        35%  { transform: rotate(3deg); }
        40%  { transform: rotate(0deg); }
        100% { transform: rotate(0deg); }
    }

    /* ── Double-pulse ("blink-blink") — 100% visible, double quick pop ── */
    @keyframes badge-double-pulse {
        0%, 100% { transform: translate(-50%, -50%) scale(1); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.6); }
        12%      { transform: translate(-50%, -50%) scale(1.3); box-shadow: 0 0 0 6px rgba(220, 38, 38, 0.4); }
        24%      { transform: translate(-50%, -50%) scale(1); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.2); }
        36%      { transform: translate(-50%, -50%) scale(1.3); box-shadow: 0 0 0 6px rgba(220, 38, 38, 0.4); }
        48%      { transform: translate(-50%, -50%) scale(1); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
    }

    .notif-bell {
        display: inline-block;
        transform-origin: top center;
    }

    /* Always animate when there are active unread notifications */
    .notif-bell.active {
        animation: bell-ring 2.5s ease-in-out infinite;
    }
    .notif-badge.active {
        animation: badge-double-pulse 1.8s ease-in-out infinite;
    }

    /* Respect reduced-motion */
    @media (prefers-reduced-motion: reduce) {
        .notif-bell.active, .notif-badge.active {
            animation: none;
        }
    }
</style>

@push('scripts')
<script>
    // ─── localStorage read tracking helpers ─────────────────────────────────
    const NOTIF_STORAGE_KEY = 'evsu_read_notif_ids_' + ({{ auth()->id() ?? 0 }});

    function getReadNotificationIds() {
        try {
            return JSON.parse(localStorage.getItem(NOTIF_STORAGE_KEY) || '[]');
        } catch(e) {
            return [];
        }
    }

    function saveReadNotificationIds(ids) {
        try {
            localStorage.setItem(NOTIF_STORAGE_KEY, JSON.stringify(ids));
        } catch(e) {}
    }

    // ─── Main Notification Initializer ──────────────────────────────────────
    window.initNotifications = function(notificationsData) {
        const notificationList  = document.getElementById('notificationList');
        const notificationBadge = document.getElementById('notificationBadge');
        const bellIcon          = document.getElementById('notificationBellIcon');
        const notifDropdown     = document.getElementById('notificationDropdown');
        const unreadPill        = document.getElementById('notificationUnreadPill');
        const markAllReadBtn    = document.getElementById('markAllReadBtn');

        if (!notificationList || !notificationBadge) return;

        const active = notificationsData || [];
        const activeIds = active.map(n => n.id);

        // Auto-prune stored IDs that no longer exist among active notifications
        let readIdsArray = getReadNotificationIds().filter(id => activeIds.includes(id));
        saveReadNotificationIds(readIdsArray);
        let readIdsSet = new Set(readIdsArray);

        function updateBadgeAndHeader() {
            const unreadCount = active.filter(n => !readIdsSet.has(n.id)).length;

            if (unreadCount > 0) {
                notificationBadge.textContent = unreadCount;
                notificationBadge.classList.remove('d-none');
                notificationBadge.classList.add('active');
                if (bellIcon) bellIcon.classList.add('active');

                if (unreadPill) {
                    unreadPill.textContent = `${unreadCount} unread`;
                    unreadPill.classList.remove('d-none');
                }
                if (markAllReadBtn) {
                    markAllReadBtn.classList.remove('d-none');
                }
            } else {
                notificationBadge.classList.add('d-none');
                notificationBadge.classList.remove('active');
                if (bellIcon) bellIcon.classList.remove('active');

                if (unreadPill) {
                    unreadPill.classList.add('d-none');
                }
                if (markAllReadBtn) {
                    markAllReadBtn.classList.add('d-none');
                }
            }
        }

        function markItemAsRead(id) {
            if (!readIdsSet.has(id)) {
                readIdsSet.add(id);
                saveReadNotificationIds(Array.from(readIdsSet));
                updateBadgeAndHeader();
                renderNotifications();
            }
        }

        function markAllAsRead() {
            activeIds.forEach(id => readIdsSet.add(id));
            saveReadNotificationIds(Array.from(readIdsSet));
            updateBadgeAndHeader();
            renderNotifications();
        }

        if (markAllReadBtn && !markAllReadBtn.dataset.bound) {
            markAllReadBtn.dataset.bound = 'true';
            markAllReadBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                markAllAsRead();
            });
        }

        function renderNotifications() {
            updateBadgeAndHeader();
            notificationList.innerHTML = '';

            if (active.length > 0) {
                active.forEach(n => {
                    const isRead = readIdsSet.has(n.id);
                    const item = document.createElement('div');
                    item.className = `p-3 d-flex align-items-start gap-3 notification-item ${isRead ? 'is-read' : 'is-unread'}`;
                    item.style.whiteSpace = 'normal';
                    item.style.cursor = 'pointer';

                    item.innerHTML = `
                        <div class="mt-1 flex-shrink-0">
                            <i class="fa-solid ${n.icon} fa-fw fs-5"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="small ${isRead ? 'text-secondary' : 'text-dark fw-semibold'} mb-1">${n.message}</div>
                            <div class="d-flex align-items-center gap-2">
                                <small class="text-muted">${n.time}</small>
                                ${isRead 
                                    ? '<span class="badge bg-light text-muted border py-0 px-1" style="font-size: 0.65rem;">Read</span>' 
                                    : '<span class="badge bg-success-subtle text-success py-0 px-1" style="font-size: 0.65rem;">New</span>'
                                }
                            </div>
                        </div>
                        <div class="flex-shrink-0 ms-1">
                            ${!isRead 
                                ? `<button type="button" class="btn btn-sm p-1 border-0 btn-mark-item-read" title="Mark as read">
                                       <i class="fa-solid fa-check"></i>
                                   </button>`
                                : `<span class="p-1 text-muted opacity-50" title="Read">
                                       <i class="fa-solid fa-check-double" style="font-size: 0.75rem;"></i>
                                   </span>`
                            }
                        </div>
                    `;

                    // Click item navigates to link and marks read
                    item.addEventListener('click', function() {
                        markItemAsRead(n.id);
                        if (window.Turbo) {
                            window.Turbo.visit(n.link);
                        } else {
                            window.location.href = n.link;
                        }
                    });

                    // Click checkmark button marks read only (no navigation)
                    const checkBtn = item.querySelector('.btn-mark-item-read');
                    if (checkBtn) {
                        checkBtn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            markItemAsRead(n.id);
                        });
                    }

                    notificationList.appendChild(item);
                });
            } else {
                notificationList.innerHTML = `
                    <div class="p-4 text-center text-muted">
                        <i class="fa-solid fa-bell-slash d-block fs-3 mb-2 opacity-50"></i>
                        <span class="small">No new notifications</span>
                    </div>
                `;
            }
        }

        renderNotifications();
    };

    // ─── Ticking Clock ───────────────────────────────────────────────────────
    (function() {
        const timeElement = document.getElementById('header-time');
        if (!timeElement) return;

        let serverTimeMs           = {{ now()->getTimestamp() * 1000 }};
        const startTimePerformance = performance.now();

        function updateClock() {
            const elapsed           = performance.now() - startTimePerformance;
            const currentServerTime = new Date(serverTimeMs + elapsed);
            let h      = currentServerTime.getHours();
            const m    = String(currentServerTime.getMinutes()).padStart(2, '0');
            const s    = String(currentServerTime.getSeconds()).padStart(2, '0');
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            timeElement.textContent  = String(h).padStart(2, '0') + ':' + m + ':' + s + ' ' + ampm;
            window.currentServerTime = currentServerTime;
        }

        updateClock();
        if (window.headerClockInterval) clearInterval(window.headerClockInterval);
        window.headerClockInterval = setInterval(updateClock, 1000);
    })();
</script>
@endpush