<?php

use App\Models\Booking;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // 1. Setup Roles
    $this->frontdeskRole = Role::create([
        'role_name' => 'FRONTDESK',
        'description' => 'Front Desk Role',
        'is_active' => true,
    ]);

    // 2. Setup Permissions
    $this->viewFolioPermission = Permission::create([
        'permission_key' => 'view-guest-list',
        'description' => 'View guest list',
        'module' => 'Front Desk',
        'is_active' => true,
    ]);

    $this->frontdeskRole->permissions()->sync([
        $this->viewFolioPermission->permission_id,
    ]);

    // 3. Create User
    $this->frontdeskUser = User::factory()->create([
        'username' => 'frontdesk_test',
        'full_name' => 'Front Desk Agent',
        'role_id' => $this->frontdeskRole->role_id,
        'is_active' => true,
    ]);

    // 4. Setup Rooms
    $this->roomA = Room::create([
        'room_number' => '101',
        'room_type' => 'Standard',
        'base_rate' => 2000.00,
        'status' => 'OCCUPIED',
        'is_active' => true,
    ]);

    $this->roomB = Room::create([
        'room_number' => '102',
        'room_type' => 'Deluxe',
        'base_rate' => 3000.00,
        'status' => 'AVAILABLE',
        'is_active' => true,
    ]);

    // 5. Setup Guests, Folios, and Bookings
    // Guest A: Checked In (In-House)
    $this->guestA = Guest::create([
        'last_name' => 'A_LastName',
        'first_name' => 'A_FirstName',
        'contact_number' => '123456789',
        'address_line1' => 'Address A',
    ]);
    $this->folioA = Folio::create([
        'folio_number' => 'FOLIO-A-100',
        'guest_id' => $this->guestA->guest_id,
        'status' => 'OPEN',
    ]);
    $this->bookingA = Booking::create([
        'folio_id' => $this->folioA->folio_id,
        'room_id' => $this->roomA->room_id,
        'arrival_date' => now()->toDateString(),
        'arrival_time' => '14:00',
        'departure_date' => now()->addDays(2)->toDateString(),
        'departure_time' => '12:00',
        'actual_check_in' => now(),
        'status' => 'CHECKED_IN',
    ]);

    // Guest B: Checked Out
    $this->guestB = Guest::create([
        'last_name' => 'B_LastName',
        'first_name' => 'B_FirstName',
        'contact_number' => '987654321',
        'address_line1' => 'Address B',
    ]);
    $this->folioB = Folio::create([
        'folio_number' => 'FOLIO-B-200',
        'guest_id' => $this->guestB->guest_id,
        'status' => 'CLOSED',
    ]);
    $this->bookingB = Booking::create([
        'folio_id' => $this->folioB->folio_id,
        'room_id' => $this->roomB->room_id,
        'arrival_date' => now()->subDays(5)->toDateString(),
        'arrival_time' => '14:00',
        'departure_date' => now()->subDays(3)->toDateString(),
        'departure_time' => '12:00',
        'actual_check_in' => now()->subDays(5),
        'actual_check_out' => now()->subDays(3),
        'status' => 'CHECKED_OUT',
    ]);

    // Guest C: No Stay (no folios or bookings)
    $this->guestC = Guest::create([
        'last_name' => 'C_LastName',
        'first_name' => 'C_FirstName',
        'contact_number' => '555555555',
        'address_line1' => 'Address C',
    ]);
});

test('unauthenticated users are redirected to login from guest list', function (): void {
    $this->get(route('frontdesk.guest-list'))
        ->assertRedirect(route('login'));
});

test('users without view-guest-list permission cannot view guest list page', function (): void {
    $nonPrivilegedRole = Role::create([
        'role_name' => 'OTHER',
        'description' => 'Other Role',
    ]);
    $nonPrivilegedUser = User::factory()->create([
        'username' => 'staff_no_perm',
        'role_id' => $nonPrivilegedRole->role_id,
    ]);

    $this->actingAs($nonPrivilegedUser)
        ->get(route('frontdesk.guest-list'))
        ->assertForbidden();
});

test('authorized users can view the guest list page with all guests', function (): void {
    $this->actingAs($this->frontdeskUser)
        ->get(route('frontdesk.guest-list'))
        ->assertOk()
        ->assertSee('A_LastName')
        ->assertSee('B_LastName')
        ->assertSee('C_LastName')
        ->assertSee('Print Guest List');
});

test('authorized users can filter guest list by checked_in', function (): void {
    $response = $this->actingAs($this->frontdeskUser)
        ->get(route('frontdesk.guest-list', ['status' => 'checked_in']))
        ->assertOk();

    // Guest A is checked in, should be visible
    $response->assertSee('A_LastName');
    // Guest A's active folio number should be displayed
    $response->assertSee('FOLIO-A-100');
    $response->assertSee('Print Checked In');

    // Guest B and C should not be visible
    $response->assertDontSee('B_LastName');
    $response->assertDontSee('C_LastName');
});

test('authorized users can filter guest list by checked_out', function (): void {
    $response = $this->actingAs($this->frontdeskUser)
        ->get(route('frontdesk.guest-list', ['status' => 'checked_out']))
        ->assertOk();

    // Guest B is checked out, should be visible
    $response->assertSee('B_LastName');
    $response->assertSee('Print Checked Out');

    // Guest A and C should not be visible
    $response->assertDontSee('A_LastName');
    $response->assertDontSee('C_LastName');
});

test('authorized users can search guest list by name', function (): void {
    $response = $this->actingAs($this->frontdeskUser)
        ->get(route('frontdesk.guest-list', ['search' => 'B_LastName']))
        ->assertOk();

    // Guest B should be visible
    $response->assertSee('B_LastName');

    // Guest A and C should not be visible
    $response->assertDontSee('A_LastName');
    $response->assertDontSee('C_LastName');
});
