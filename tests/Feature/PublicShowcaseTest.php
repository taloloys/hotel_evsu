<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    Cache::forget('public_showcase_data');
});

it('loads the pure public showcase landing page on root route', function (): void {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('EVSU');
    $response->assertSee('Ormoc Campus');
    $response->assertDontSee('Main Campus');
    $response->assertSee('htmd.evsuoc@gmail.com');
    $response->assertSee('Standard Room');
    $response->assertSee('Superior Room');
    $response->assertSee('Senior Suite');
    $response->assertSee('TWIN ROOM 1 TWIN BED 2 BEDROOMS SEA VIEW');
    $response->assertSee('EVSU Cafeteria & Lounge', false);
    $response->assertSee('Staff / Guest Login');
});

it('does not put authenticated users into preview mode on the public root route', function (): void {
    $role = Role::create([
        'role_name' => 'ADMIN',
        'description' => 'Administrator',
    ]);

    $admin = User::factory()->create([
        'role_id' => $role->role_id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->get('/');

    $response->assertStatus(200);
    $response->assertDontSee('Preview Mode');
    $response->assertSee('Go to Dashboard');
    $response->assertSee(route('admin.dashboard'), false);
});

it('keeps preview mode limited to the dedicated admin preview route', function (): void {
    $permission = Permission::create([
        'permission_key' => 'manage-landing-page',
        'description' => 'Manage landing page',
        'module' => 'System',
        'is_active' => true,
    ]);

    $role = Role::create([
        'role_name' => 'ADMIN',
        'description' => 'Administrator',
    ]);
    $role->permissions()->attach($permission->permission_id);

    $admin = User::factory()->create([
        'role_id' => $role->role_id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.landing-page.preview'));

    $response->assertStatus(200);
    $response->assertSee('Preview Mode');
    $response->assertSee('Admin Preview');
});

it('loads the separate dedicated login page on login route', function (): void {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Welcome Back');
    $response->assertSee('name="username"', false);
    $response->assertSee('name="password"', false);
    $response->assertSee('Back to Showcase');
});

it('renders all 10 hotel room options in the public showcase catalog without descriptions', function (): void {
    $response = $this->get('/');

    $response->assertStatus(200);

    $requiredRooms = [
        'Standard Room',
        'Superior Room',
        'Standard Twin Room',
        '2 Bedrooms, Balcony',
        'Superior Double or Twin Room, 2 Bedrooms',
        'Superior Double or Twin Room, 1 Bedroom, Non Smoking, Sea View',
        'Senior Suite',
        'TWIN ROOM 1 TWIN BED 2 BEDROOMS SEA VIEW',
        'SUPERIOR DOUBLE OR TWIN ROOM 1 TWIN BED 2 BEDROOMS',
        'Twin room - 2 Bedrooms',
    ];

    foreach ($requiredRooms as $roomName) {
        $response->assertSee($roomName, false);
    }
});

it('authenticates user successfully through dedicated login page', function (): void {
    $role = Role::create([
        'role_name' => 'ADMIN',
        'description' => 'Administrator',
    ]);

    $user = User::factory()->create([
        'username' => 'showcase_admin',
        'password_hash' => Hash::make('password123'),
        'role_id' => $role->role_id,
        'is_active' => true,
    ]);

    $response = $this->withoutMiddleware(ValidateCsrfToken::class)->post('/login', [
        'username' => 'showcase_admin',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($user);
});

it('displays dynamic rooms from the database when rooms are present', function (): void {
    Room::create([
        'room_number' => '999',
        'room_type' => 'Ultimate Palace Suite',
        'base_rate' => 9999.00,
        'status' => 'AVAILABLE',
        'is_active' => true,
    ]);

    // Clear cache
    Cache::forget('public_showcase_data');

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Ultimate Palace Suite');
    $response->assertSee('₱9,999 / night');
});
