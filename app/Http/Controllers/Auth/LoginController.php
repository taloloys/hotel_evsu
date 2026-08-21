<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\LandingPageShowcase;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showcase(): View
    {
        $showcaseData = Cache::remember('public_showcase_data', 3600, function () {
            $dbRooms = Room::where('is_active', true)->orderBy('room_number')->get();

            if ($dbRooms->isNotEmpty()) {
                $roomsGrouped = $dbRooms->groupBy('room_type');
                $rooms = [];
                $index = 1;

                // Load all showcase configurations to match by room_type title
                $showcases = LandingPageShowcase::where('type', 'ROOM')->get()->keyBy(function ($item) {
                    return strtolower(trim($item->title));
                });

                foreach ($roomsGrouped as $roomType => $roomsInGroup) {
                    $firstRoom = $roomsInGroup->first();
                    $baseRate = (float) $firstRoom->base_rate;
                    $key = strtolower(trim($roomType));

                    if ($showcases->has($key)) {
                        $sc = $showcases->get($key);
                        $scImages = [];
                        if (is_array($sc->images)) {
                            $scImages = array_filter($sc->images, function ($img) {
                                return ! empty($img) && file_exists(public_path($img));
                            });
                        }
                        if (empty($scImages)) {
                            $scImages = $this->determineRoomImages($roomType);
                        }

                        $rooms[] = [
                            'id' => $sc->id,
                            'name' => $roomType,
                            'category' => $sc->category ?? (str_replace(' Room', '', $roomType) ?: 'Standard'),
                            'capacity' => $sc->capacity ?? $this->determineRoomCapacity($roomType),
                            'price' => '₱'.number_format($baseRate).' / night',
                            'images' => array_values($scImages),
                            'icon' => $sc->icon ?? $this->determineRoomIcon($roomType),
                            'badge' => $sc->badge ?? $this->determineRoomBadge($roomType, $baseRate),
                            'is_active' => $sc->is_active,
                        ];
                    } else {
                        $rooms[] = [
                            'id' => $index++,
                            'name' => $roomType,
                            'category' => str_replace(' Room', '', $roomType) ?: 'Standard',
                            'capacity' => $this->determineRoomCapacity($roomType),
                            'price' => '₱'.number_format($baseRate).' / night',
                            'images' => $this->determineRoomImages($roomType),
                            'icon' => $this->determineRoomIcon($roomType),
                            'badge' => $this->determineRoomBadge($roomType, $baseRate),
                            'is_active' => true,
                        ];
                    }
                }

                // Filter out rooms configured as hidden/inactive in showcase settings
                $rooms = array_filter($rooms, function ($r) {
                    return $r['is_active'];
                });
                $rooms = array_values($rooms);
            } else {
                $rooms = [
                    [
                        'id' => 1,
                        'name' => 'Standard Room',
                        'category' => 'Standard',
                        'capacity' => '2 Guests',
                        'price' => '₱2,500 / night',
                        'images' => ['images/showcase/rooms/standard.jpg'],
                        'icon' => 'fa-bed',
                        'badge' => 'Popular',
                    ],
                    [
                        'id' => 2,
                        'name' => 'Superior Room',
                        'category' => 'Superior',
                        'capacity' => '2-3 Guests',
                        'price' => '₱3,200 / night',
                        'images' => ['images/showcase/rooms/superior.jpg'],
                        'icon' => 'fa-star',
                        'badge' => 'Best Value',
                    ],
                    [
                        'id' => 3,
                        'name' => 'Standard Twin Room',
                        'category' => 'Standard',
                        'capacity' => '2 Guests (Twin Beds)',
                        'price' => '₱2,700 / night',
                        'images' => ['images/showcase/rooms/standard_twin.jpg'],
                        'icon' => 'fa-users',
                        'badge' => null,
                    ],
                    [
                        'id' => 4,
                        'name' => '2 Bedrooms, Balcony',
                        'category' => 'Family Suite',
                        'capacity' => '4 Guests',
                        'price' => '₱4,800 / night',
                        'images' => ['images/showcase/rooms/two_bedrooms_balcony.jpg'],
                        'icon' => 'fa-building-columns',
                        'badge' => 'Balcony View',
                    ],
                    [
                        'id' => 5,
                        'name' => 'Superior Double or Twin Room, 2 Bedrooms',
                        'category' => 'Superior Family',
                        'capacity' => '4-5 Guests',
                        'price' => '₱5,200 / night',
                        'images' => ['images/showcase/rooms/superior_2bedrooms.jpg'],
                        'icon' => 'fa-people-roof',
                        'badge' => null,
                    ],
                    [
                        'id' => 6,
                        'name' => 'Superior Double or Twin Room, 1 Bedroom, Non Smoking, Sea View',
                        'category' => 'Superior Oceanfront',
                        'capacity' => '2 Guests',
                        'price' => '₱4,200 / night',
                        'images' => ['images/showcase/rooms/superior_seaview.jpg'],
                        'icon' => 'fa-water',
                        'badge' => 'Sea View',
                    ],
                    [
                        'id' => 7,
                        'name' => 'Senior Suite',
                        'category' => 'Luxury Suite',
                        'capacity' => '2-4 Guests',
                        'price' => '₱6,500 / night',
                        'images' => ['images/showcase/rooms/senior_suite.jpg'],
                        'icon' => 'fa-crown',
                        'badge' => 'Luxury Premium',
                    ],
                    [
                        'id' => 8,
                        'name' => 'TWIN ROOM 1 TWIN BED 2 BEDROOMS SEA VIEW',
                        'category' => 'Suite Sea View',
                        'capacity' => '4 Guests',
                        'price' => '₱5,600 / night',
                        'images' => ['images/showcase/rooms/twin_seaview.jpg'],
                        'icon' => 'fa-umbrella-beach',
                        'badge' => 'Sea View',
                    ],
                    [
                        'id' => 9,
                        'name' => 'SUPERIOR DOUBLE OR TWIN ROOM 1 TWIN BED 2 BEDROOMS',
                        'category' => 'Superior Suite',
                        'capacity' => '4 Guests',
                        'price' => '₱5,400 / night',
                        'images' => ['images/showcase/rooms/superior_twin_2bedrooms.jpg'],
                        'icon' => 'fa-hotel',
                        'badge' => null,
                    ],
                    [
                        'id' => 10,
                        'name' => 'Twin room - 2 Bedrooms',
                        'category' => 'Family Twin',
                        'capacity' => '4 Guests',
                        'price' => '₱4,500 / night',
                        'images' => ['images/showcase/rooms/twin_2bedrooms.jpg'],
                        'icon' => 'fa-door-open',
                        'badge' => null,
                    ],
                ];
            }

            $mainCafeteria = LandingPageShowcase::where('type', 'CAFETERIA_MAIN')->first();
            $cafeteriaHero = [
                'title' => $mainCafeteria->title ?? 'Savor Handcrafted Coffee & Gourmet Culinary Treats',
                'category' => $mainCafeteria->category ?? 'EVSU Cafeteria & Lounge',
                'timing' => $mainCafeteria->timing ?? 'Open daily 6:30 AM - 10:00 PM',
                'image' => ! empty($mainCafeteria->images[0]) ? $mainCafeteria->images[0] : 'images/showcase/coffeeshop/cafeteria_main.jpg',
            ];

            $dbItems = LandingPageShowcase::where('type', 'CAFETERIA_ITEM')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            if ($dbItems->isNotEmpty()) {
                $coffeeshopHighlights = $dbItems->map(function ($item) {
                    return [
                        'title' => $item->title,
                        'category' => $item->category,
                        'timing' => $item->timing,
                        'image' => ! empty($item->images[0]) ? $item->images[0] : 'images/showcase/coffeeshop/coffee.jpg',
                        'icon' => $item->icon ?? 'fa-mug-hot',
                    ];
                })->toArray();
            } else {
                $coffeeshopHighlights = [
                    [
                        'title' => 'Artisan Espresso & Specialty Brews',
                        'category' => 'Coffee & Beverages',
                        'image' => 'images/showcase/coffeeshop/coffee.jpg',
                        'icon' => 'fa-mug-hot',
                        'timing' => 'Served All Day',
                    ],
                    [
                        'title' => 'Freshly Baked Pastries & Artisan Cakes',
                        'category' => 'Bakery & Sweets',
                        'image' => 'images/showcase/coffeeshop/pastries.jpg',
                        'icon' => 'fa-stroopwafel',
                        'timing' => 'Fresh Daily at 7:00 AM',
                    ],
                    [
                        'title' => 'Gourmet Breakfast & Savory Plates',
                        'category' => 'All-Day Dining',
                        'image' => 'images/showcase/coffeeshop/breakfast.jpg',
                        'icon' => 'fa-utensils',
                        'timing' => '6:30 AM - 9:00 PM',
                    ],
                    [
                        'title' => 'Cozy Lounge & Relaxing Ambience',
                        'category' => 'Atmosphere',
                        'image' => 'images/showcase/coffeeshop/ambience.jpg',
                        'icon' => 'fa-couch',
                        'timing' => 'Open 6:30 AM - 10:00 PM',
                    ],
                ];
            }

            return compact('rooms', 'cafeteriaHero', 'coffeeshopHighlights');
        });

        return view('public.showcase', $showcaseData);
    }

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $credentials['is_active'] = true;

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'username' => 'The provided credentials are incorrect.',
                ])
                ->onlyInput('username');
        }

        ActivityLog::log('LOGIN', 'User logged in successfully.');

        $request->session()->regenerate();

        /** @var User|null $user */
        $user = Auth::user();
        $targetRoute = $this->dashboardRouteForUser($user);

        $intendedUrl = $request->session()->get('url.intended');
        if (! $intendedUrl || $intendedUrl === route('home') || $intendedUrl === url('/')) {
            $request->session()->forget('url.intended');

            return redirect()
                ->to($targetRoute)
                ->with('show_login_confirmation', true);
        }

        return redirect()
            ->intended($targetRoute)
            ->with('show_login_confirmation', true);
    }

    public function logout(Request $request): RedirectResponse
    {
        ActivityLog::log('LOGOUT', 'User logged out.');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function dashboardRouteForUser(?User $user): string
    {
        if (! $user) {
            return route('home');
        }

        $roleName = strtoupper(trim($user->role?->role_name ?? ''));
        $normalizedRole = str_replace(['_', ' ', '-'], '', $roleName);

        // 1. Direct match on role names (handles FRONTDESK, FRONT_DESK, etc.)
        if (in_array($normalizedRole, ['SUPERADMIN', 'ADMIN'], true)) {
            return route('admin.dashboard');
        }
        if (in_array($normalizedRole, ['FRONTDESK', 'FRONTDESKOPERATIONS'], true)) {
            return route('frontdesk.dashboard');
        }
        if (in_array($normalizedRole, ['ACCOUNTING', 'FINANCE'], true)) {
            return route('accounting.dashboard');
        }
        if (in_array($normalizedRole, ['CAFETERIA', 'POS', 'COFFEESHOP'], true)) {
            return route('coffeeshop.dashboard');
        }

        // 2. Dynamic permission check for custom roles
        if ($user->isAdmin() || $user->hasPermission('manage-users') || $user->hasPermission('manage-landing-page') || $user->hasPermission('manage-roles') || $user->hasPermission('manage-backup-restore')) {
            return route('admin.dashboard');
        }

        if (
            $user->hasPermission('manage-reservations') ||
            $user->hasPermission('view-guest-list') ||
            $user->hasPermission('view-guest-folio') ||
            $user->hasPermission('manage-guest-folio') ||
            $user->hasPermission('process-checkout') ||
            $user->hasPermission('view-shift-sales')
        ) {
            return route('frontdesk.dashboard');
        }

        if (
            $user->hasPermission('view-accounting-dashboard') ||
            $user->hasPermission('manage-accounting-billing') ||
            $user->hasPermission('manage-accounting-payments') ||
            $user->hasPermission('manage-accounting-expenses') ||
            $user->hasPermission('manage-accounting-receivables') ||
            $user->hasPermission('view-accounting-reports') ||
            $user->hasPermission('view-accounting-audit')
        ) {
            return route('accounting.dashboard');
        }

        if ($user->hasPermission('manage-inventory')) {
            return route('coffeeshop.dashboard');
        }

        return route('home');
    }

    private function determineRoomCapacity(string $roomType): string
    {
        $lower = strtolower($roomType);
        if (str_contains($lower, 'single')) {
            return '1 Guest';
        }
        if (str_contains($lower, 'twin') || str_contains($lower, 'double') || str_contains($lower, 'studio')) {
            return '2 Guests';
        }
        if (str_contains($lower, 'triple')) {
            return '3 Guests';
        }
        if (str_contains($lower, 'connecting') || str_contains($lower, 'family') || str_contains($lower, 'suite')) {
            return '4-5 Guests';
        }

        return '2 Guests';
    }

    private function determineRoomIcon(string $roomType): string
    {
        $lower = strtolower($roomType);
        if (str_contains($lower, 'suite') || str_contains($lower, 'president') || str_contains($lower, 'deluxe')) {
            return 'fa-crown';
        }
        if (str_contains($lower, 'twin') || str_contains($lower, 'double')) {
            return 'fa-users';
        }
        if (str_contains($lower, 'connecting') || str_contains($lower, 'family')) {
            return 'fa-people-roof';
        }

        return 'fa-bed';
    }

    private function determineRoomBadge(string $roomType, float $baseRate): ?string
    {
        $lower = strtolower($roomType);
        if (str_contains($lower, 'president') || str_contains($lower, 'senior')) {
            return 'VIP Exclusive';
        }
        if ($baseRate >= 200) {
            return 'Luxury Premium';
        }
        if ($baseRate <= 60) {
            return 'Best Value';
        }
        if (str_contains($lower, 'deluxe') || str_contains($lower, 'studio')) {
            return 'Popular';
        }

        return null;
    }

    private function determineRoomImages(string $roomType): array
    {
        $slug = strtolower(str_replace([' ', ','], ['_', ''], $roomType));
        $defaultImg = "images/showcase/rooms/{$slug}.jpg";

        if (file_exists(public_path($defaultImg))) {
            return [$defaultImg];
        }

        return [];
    }
}
