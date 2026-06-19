<?php

use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // Setup admin role
    $this->adminRole = Role::create([
        'role_name' => 'ADMIN',
        'description' => 'Admin Role',
        'is_active' => true,
    ]);

    // Create admin user
    $this->adminUser = User::factory()->create([
        'role_id' => $this->adminRole->role_id,
        'is_active' => true,
    ]);

    // Create a few default rooms
    $this->availableRoom = Room::create([
        'room_number' => '101',
        'room_type' => 'Single Room',
        'base_rate' => 2500.00,
        'status' => 'AVAILABLE',
        'is_active' => true,
    ]);

    $this->occupiedRoom = Room::create([
        'room_number' => '102',
        'room_type' => 'Deluxe Room',
        'base_rate' => 3800.00,
        'status' => 'OCCUPIED',
        'is_active' => true,
    ]);

    $this->inactiveRoom = Room::create([
        'room_number' => '103',
        'room_type' => 'Suite',
        'base_rate' => 6500.00,
        'status' => 'MAINTENANCE',
        'is_active' => false,
    ]);
});

test('unauthenticated users are redirected to login', function (): void {
    $this->get(route('admin.rooms'))
        ->assertRedirect(route('login'));
});

test('authenticated admin can view the rooms management page with stats', function (): void {
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.rooms'));

    $response->assertOk();
    $response->assertSee('101');
    $response->assertSee('Single Room');
    $response->assertSee('102');
    $response->assertSee('Deluxe Room');
    $response->assertSee('103');
    $response->assertSee('Suite');

    // Verification of virtual floor accessor rendering
    $response->assertSee('1st Floor');

    // Verification of KPI stats
    $response->assertSee('3'); // Total rooms count
    $response->assertSee('1'); // Occupied count
    $response->assertSee('1'); // Available count
});

test('admin can create a new room with valid data', function (): void {
    $roomData = [
        'room_number' => '201',
        'room_type' => 'President Suite',
        'base_rate' => 12000.00,
        'status' => 'AVAILABLE',
    ];

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.rooms.store'), $roomData);

    $response->assertRedirect(route('admin.rooms'));
    $response->assertSessionHas('success', 'Room created successfully.');

    $this->assertDatabaseHas('rooms', [
        'room_number' => '201',
        'room_type' => 'President Suite',
        'base_rate' => 12000.00,
        'status' => 'AVAILABLE',
        'is_active' => true,
    ]);
});

test('room creation fails if room number already exists', function (): void {
    $roomData = [
        'room_number' => '101', // Existing
        'room_type' => 'Twin Room',
        'base_rate' => 3000.00,
        'status' => 'AVAILABLE',
    ];

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.rooms.store'), $roomData);

    $response->assertSessionHasErrors(['room_number']);
});

test('admin can update an existing room', function (): void {
    $updateData = [
        'room_number' => '101-A',
        'room_type' => 'Premium Single',
        'base_rate' => 2800.00,
        'status' => 'MAINTENANCE',
    ];

    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.rooms.update', $this->availableRoom), $updateData);

    $response->assertRedirect(route('admin.rooms'));
    $response->assertSessionHas('success', 'Room updated successfully.');

    $this->assertDatabaseHas('rooms', [
        'room_id' => $this->availableRoom->room_id,
        'room_number' => '101-A',
        'room_type' => 'Premium Single',
        'base_rate' => 2800.00,
        'status' => 'MAINTENANCE',
    ]);
});

test('admin can toggle active status of a room', function (): void {
    // Disable active room
    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.rooms.toggle', $this->availableRoom));

    $response->assertRedirect(route('admin.rooms'));
    $response->assertSessionHas('success', 'Room has been successfully disabled.');
    expect($this->availableRoom->fresh()->is_active)->toBeFalse();

    // Enable inactive room
    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.rooms.toggle', $this->availableRoom));

    $response->assertRedirect(route('admin.rooms'));
    $response->assertSessionHas('success', 'Room has been successfully enabled.');
    expect($this->availableRoom->fresh()->is_active)->toBeTrue();
});

test('admin cannot disable an occupied room', function (): void {
    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.rooms.toggle', $this->occupiedRoom));

    $response->assertRedirect(route('admin.rooms'));
    $response->assertSessionHasErrors(['cannot_disable_occupied']);
    expect($this->occupiedRoom->fresh()->is_active)->toBeTrue();
});

test('admin cannot disable a reserved room', function (): void {
    $reservedRoom = Room::create([
        'room_number' => '104',
        'room_type' => 'Single Room',
        'base_rate' => 2500.00,
        'status' => 'RESERVED',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->patch(route('admin.rooms.toggle', $reservedRoom));

    $response->assertRedirect(route('admin.rooms'));
    $response->assertSessionHasErrors(['cannot_disable_occupied']);
    expect($reservedRoom->fresh()->is_active)->toBeTrue();
});

test('inactive rooms are excluded from assignable rooms in front desk dashboard and reservation list', function (): void {
    // Set up active available room
    $activeAvailable = Room::create([
        'room_number' => '105',
        'room_type' => 'Single Room',
        'base_rate' => 2500.00,
        'status' => 'AVAILABLE',
        'is_active' => true,
    ]);

    // Set up inactive available room
    $inactiveAvailable = Room::create([
        'room_number' => '106',
        'room_type' => 'Single Room',
        'base_rate' => 2500.00,
        'status' => 'AVAILABLE',
        'is_active' => false,
    ]);

    // View dashboard (DashboardController index)
    $response = $this->actingAs($this->adminUser)
        ->get(route('frontdesk.dashboard'));

    $response->assertOk();
    // Active available room (105) should be listed, inactive (106) should not be visible or counted in available stats
    $response->assertSee('105');
    $response->assertDontSee('106');

    // View reservations page (ReservationController index)
    $response = $this->actingAs($this->adminUser)
        ->get(route('frontdesk.reservation'));

    $response->assertOk();
    $response->assertSee('105');
    $response->assertDontSee('106');
});
