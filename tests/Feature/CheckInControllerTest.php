<?php

use App\Models\Guest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // Setup FRONT_DESK role
    $this->frontdeskRole = Role::create([
        'role_name' => 'FRONT_DESK',
        'description' => 'Front Desk Role',
        'is_active' => true,
    ]);

    // Setup Permission
    $this->reservationsPermission = Permission::create([
        'permission_key' => 'manage-reservations',
        'description' => 'Manage reservations',
        'module' => 'Front Desk',
        'is_active' => true,
    ]);

    $this->frontdeskRole->permissions()->sync([
        $this->reservationsPermission->permission_id,
    ]);

    // Create front desk user
    $this->frontdeskUser = User::factory()->create([
        'username' => 'frontdesk_test',
        'full_name' => 'Front Desk Person',
        'role_id' => $this->frontdeskRole->role_id,
        'is_active' => true,
    ]);

    // Create a Room
    $this->room = Room::create([
        'room_number' => '101',
        'room_type' => 'Standard',
        'base_rate' => 2000.00,
        'status' => 'AVAILABLE',
        'is_active' => true,
    ]);

    // Create a Guest
    $this->guest = Guest::create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'contact_number' => '09123456789',
        'address_line1' => 'Manila, Philippines',
    ]);
});

test('authenticated staff can view check-in page', function (): void {
    $response = $this->actingAs($this->frontdeskUser)
        ->get(route('frontdesk.checkin'));

    $response->assertOk();
    $response->assertSee('Existing Guest Check In');
    $response->assertSee('John');
    $response->assertSee('Doe');
});

test('authenticated staff can check in existing guest', function (): void {
    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.checkin.store'), [
            'guest_id' => $this->guest->guest_id,
            'payment_method' => 'Cash',
            'room_id' => $this->room->room_id,
            'arrival_date' => now()->toDateString(),
            'arrival_time' => '14:00',
            'departure_date' => now()->addDays(2)->toDateString(),
            'departure_time' => '11:00',
            'num_pax' => 1,
            'market_segment' => 'Walk-in',
        ]);

    $response->assertRedirect(route('frontdesk.dashboard'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('folios', [
        'guest_id' => $this->guest->guest_id,
        'payment_method' => 'Cash',
        'status' => 'OPEN',
        'net_rate' => 2000.00,
    ]);

    $this->assertDatabaseHas('bookings', [
        'room_id' => $this->room->room_id,
        'status' => 'CHECKED_IN',
    ]);

    $this->assertDatabaseHas('rooms', [
        'room_id' => $this->room->room_id,
        'status' => 'OCCUPIED',
    ]);
});
