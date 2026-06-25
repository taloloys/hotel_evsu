@php
    $user = auth()->user();
    $notifications = [];
    $initials = 'DF'; // Default fallback

    if ($user) {
        // Calculate Initials
        if ($user->full_name) {
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

        // 1. Manage Reservations notifications
        if ($user->hasPermission('manage-reservations')) {
            $today = \Carbon\Carbon::today();
            $pendingCheckins = \App\Models\Booking::where('status', 'RESERVED')
                ->whereDate('arrival_date', '<=', $today)
                ->with(['folio.guest', 'room'])
                ->get();

            foreach ($pendingCheckins as $booking) {
                $guestName = $booking->folio && $booking->folio->guest 
                    ? ($booking->folio->guest->first_name . ' ' . $booking->folio->guest->last_name) 
                    : 'Unknown Guest';
                $roomNumber = $booking->room ? $booking->room->room_number : 'Unassigned';
                $notifications[] = [
                    'id' => 'booking-checkin-' . $booking->booking_id,
                    'type' => 'reservation',
                    'icon' => 'fa-calendar-check text-success',
                    'message' => "Guest {$guestName} is pending check-in (Room {$roomNumber}).",
                    'link' => route('frontdesk.checkin'),
                    'time' => $booking->arrival_date->format('M d, Y'),
                ];
            }

            // Dirty Rooms
            $dirtyRooms = \App\Models\Room::where('status', 'CLEANING')
                ->where('is_active', true)
                ->get();

            foreach ($dirtyRooms as $room) {
                $notifications[] = [
                    'id' => 'room-dirty-' . $room->room_id,
                    'type' => 'housekeeping',
                    'icon' => 'fa-broom text-warning',
                    'message' => "Room {$room->room_number} requires cleaning.",
                    'link' => route('frontdesk.dashboard'),
                    'time' => 'Action required',
                ];
            }

            // Rooms under Maintenance
            $maintenanceRooms = \App\Models\Room::where('status', 'MAINTENANCE')
                ->where('is_active', true)
                ->get();

            foreach ($maintenanceRooms as $room) {
                $notifications[] = [
                    'id' => 'room-maintenance-' . $room->room_id,
                    'type' => 'maintenance',
                    'icon' => 'fa-wrench text-danger',
                    'message' => "Room {$room->room_number} is under maintenance.",
                    'link' => route('frontdesk.dashboard'),
                    'time' => 'In progress',
                ];
            }
        }

        // 2. View Folio / Process Checkout notifications
        if ($user->hasPermission('view-folio') || $user->hasPermission('process-checkout')) {
            $today = \Carbon\Carbon::today();
            $pendingCheckouts = \App\Models\Booking::where('status', 'CHECKED_IN')
                ->whereDate('departure_date', '<=', $today)
                ->with(['folio.guest', 'room'])
                ->get();

            foreach ($pendingCheckouts as $booking) {
                $guestName = $booking->folio && $booking->folio->guest 
                    ? ($booking->folio->guest->first_name . ' ' . $booking->folio->guest->last_name) 
                    : 'Unknown Guest';
                $roomNumber = $booking->room ? $booking->room->room_number : 'Unassigned';
                $notifications[] = [
                    'id' => 'booking-checkout-' . $booking->booking_id,
                    'type' => 'checkout',
                    'icon' => 'fa-door-open text-primary',
                    'message' => "Guest {$guestName} (Room {$roomNumber}) is due for checkout today.",
                    'link' => route('frontdesk.guest-folio'),
                    'time' => $booking->departure_date->format('M d, Y'),
                ];
            }

            // Pending Expenses
            $pendingExpenses = \App\Models\Expense::where('status', 'PENDING')->get();
            foreach ($pendingExpenses as $expense) {
                $notifications[] = [
                    'id' => 'expense-pending-' . $expense->expense_id,
                    'type' => 'billing',
                    'icon' => 'fa-file-invoice-dollar text-info',
                    'message' => "Expense: {$expense->description} (₱" . number_format($expense->amount, 2) . ") needs approval.",
                    'link' => route('accounting.expenses'),
                    'time' => $expense->expense_date->format('M d, Y'),
                ];
            }
        }

        // 3. Manage Inventory notifications
        if ($user->hasPermission('manage-inventory')) {
            $notifications[] = [
                'id' => 'inventory-low-beans',
                'type' => 'inventory',
                'icon' => 'fa-box-open text-danger',
                'message' => "Low Stock: 'Coffee Beans' is below 5kg (Current: 1.2kg).",
                'link' => route('coffeeshop.inventory'),
                'time' => 'Low Stock',
            ];
            $notifications[] = [
                'id' => 'inventory-low-milk',
                'type' => 'inventory',
                'icon' => 'fa-box-open text-danger',
                'message' => "Low Stock: 'Fresh Milk' is below 10 liters (Current: 3.0L).",
                'link' => route('coffeeshop.inventory'),
                'time' => 'Low Stock',
            ];
        }

        // 4. Manage Shifts notifications
        if ($user->hasPermission('manage-shifts') || $user->hasPermission('manage-reservations')) {
            $activeShift = \App\Models\Shift::where('user_id', $user->user_id)
                ->whereNull('end_time')
                ->first();

            if (!$activeShift) {
                $notifications[] = [
                    'id' => 'shift-none-open',
                    'type' => 'shift',
                    'icon' => 'fa-clock-rotate-left text-warning',
                    'message' => "No active shift open. Please start a shift.",
                    'link' => route('frontdesk.dashboard'),
                    'time' => 'Attention',
                ];
            }
        }
    }
@endphp

<div class="card border-0 shadow-sm mb-4">

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

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 py-0 overflow-hidden"
                        aria-labelledby="notificationDropdown"
                        style="width: 320px; max-height: 400px; overflow-y: auto; z-index: 1050;">

                        <li class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                            <span class="fw-bold">Notifications</span>
                            <button class="btn btn-sm btn-link text-decoration-none p-0 text-primary fw-semibold" id="clearAllNotifications">Clear All</button>
                        </li>

                        <div id="notificationList">
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
    document.addEventListener('DOMContentLoaded', function() {
        // Ticking clock
        const timeElement = document.getElementById('header-time');
        if (timeElement) {
            function updateClock() {
                const now = new Date();
                let hours = now.getHours();
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12; // the hour '0' should be '12'
                const hoursStr = String(hours).padStart(2, '0');
                timeElement.textContent = hoursStr + ':' + minutes + ':' + seconds + ' ' + ampm;
            }
            updateClock();
            setInterval(updateClock, 1000);
        }

        // Notification center
        const notifications = @json($notifications);
        const notificationList = document.getElementById('notificationList');
        const notificationBadge = document.getElementById('notificationBadge');
        const clearAllBtn = document.getElementById('clearAllNotifications');

        function getDismissed() {
            try {
                return JSON.parse(localStorage.getItem('dismissed_notifications') || '[]');
            } catch (e) {
                return [];
            }
        }

        function setDismissed(ids) {
            localStorage.setItem('dismissed_notifications', JSON.stringify(ids));
        }

        function renderNotifications() {
            const dismissed = getDismissed();
            const active = notifications.filter(n => !dismissed.includes(n.id));

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
                            <i class="fa-solid \${n.icon} fa-fw fs-5"></i>
                        </div>
                        <div class="flex-grow-1 pe-3" onclick="window.location.href='\${n.link}'">
                            <div class="small text-dark mb-1">\${n.message}</div>
                            <small class="text-muted text-xs">\${n.time}</small>
                        </div>
                        <button class="btn btn-sm btn-link text-muted p-0 position-absolute dismiss-btn" 
                                style="top: 10px; right: 10px;" 
                                data-dismiss-id="\${n.id}"
                                title="Dismiss">
                            <i class="fa-solid fa-xmark fs-6"></i>
                        </button>
                    `;
                    
                    // Handle dismiss click specifically to avoid triggering the list item click
                    item.querySelector('.dismiss-btn').addEventListener('click', function(e) {
                        e.stopPropagation();
                        e.preventDefault();
                        dismissNotification(n.id);
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

        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const dismissed = getDismissed();
                notifications.forEach(n => {
                    if (!dismissed.includes(n.id)) {
                        dismissed.push(n.id);
                    }
                });
                setDismissed(dismissed);
                renderNotifications();
            });
        }

        renderNotifications();
    });
</script>
@endpush