<?php

use App\Models\Folio;
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
});

test('authenticated staff can view registration page and see defaults', function (): void {
    $response = $this->actingAs($this->frontdeskUser)
        ->get(route('frontdesk.registration'));

    $response->assertOk();
    $response->assertSee('Guest Registration');
    // Check that defaults are 12:00 PM (12:00)
    $response->assertSee('value="12:00"', false);
});

test('authenticated staff can register a new guest and auto post room charges', function (): void {
    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.registration.store'), [
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'contact_number' => '09998887777',
            'address_line1' => 'Cebu City',
            'payment_method' => 'Cash',
            'room_id' => $this->room->room_id,
            'arrival_date' => now()->toDateString(),
            'arrival_time' => '12:00',
            'departure_date' => now()->addDays(2)->toDateString(),
            'departure_time' => '12:00',
            'num_pax' => 1,
            'market_segment' => 'Walk-in',
        ]);

    $response->assertRedirect(route('frontdesk.dashboard'));
    $response->assertSessionHas('success');

    // Guest and folio created
    $this->assertDatabaseHas('guests', [
        'first_name' => 'Alice',
        'last_name' => 'Smith',
    ]);

    $guest = Guest::where('first_name', 'Alice')->where('last_name', 'Smith')->first();

    $this->assertDatabaseHas('folios', [
        'guest_id' => $guest->guest_id,
        'status' => 'OPEN',
    ]);

    $folio = Folio::where('guest_id', $guest->guest_id)->first();

    // Booking created
    $this->assertDatabaseHas('bookings', [
        'folio_id' => $folio->folio_id,
        'room_id' => $this->room->room_id,
        'status' => 'CHECKED_IN',
    ]);

    // Verify auto-posted room charges night-by-night (only Night 1 posted at first)
    $this->assertDatabaseHas('transactions', [
        'folio_id' => $folio->folio_id,
        'charge_code' => 100,
        'charge_amount' => 2000.00,
        'reference_notes' => 'Room charge for Night 1 (Date: '.now()->toDateString().')',
    ]);

    $this->assertDatabaseMissing('transactions', [
        'folio_id' => $folio->folio_id,
        'charge_code' => 100,
        'charge_amount' => 2000.00,
        'reference_notes' => 'Room charge for Night 2 (Date: '.now()->addDays(1)->toDateString().')',
    ]);

    // Travel to the next day
    $this->travel(1)->days();

    // Run the nightly charge command
    $this->artisan('app:post-nightly-room-charges')
        ->assertExitCode(0);

    // Verify Night 2 is now posted
    $this->assertDatabaseHas('transactions', [
        'folio_id' => $folio->folio_id,
        'charge_code' => 100,
        'charge_amount' => 2000.00,
        'reference_notes' => 'Room charge for Night 2 (Date: '.now()->toDateString().')',
    ]);
});

test('registering a guest with the same name as an existing guest creates a new guest and folio', function (): void {
    // Pre-create existing guest and folio
    $existingGuest = Guest::create([
        'first_name' => 'Alice',
        'last_name' => 'Smith',
        'contact_number' => '09998887777',
    ]);

    $existingFolio = Folio::create([
        'folio_number' => 'REG-2026888',
        'registration_number' => 'REGNUM-2026888',
        'guest_id' => $existingGuest->guest_id,
        'status' => 'CLOSED',
        'net_rate' => 1800.00,
    ]);

    // Register with same name but without passing the same folio/registration number (to avoid unique validation crash)
    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.registration.store'), [
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'contact_number' => '09998887777',
            'address_line1' => 'Cebu City',
            'payment_method' => 'Cash',
            'room_id' => $this->room->room_id,
            'arrival_date' => now()->toDateString(),
            'arrival_time' => '12:00',
            'departure_date' => now()->addDays(1)->toDateString(),
            'departure_time' => '12:00',
            'num_pax' => 1,
            'market_segment' => 'Walk-in',
        ]);

    $response->assertRedirect(route('frontdesk.dashboard'));
    $response->assertSessionHas('success');

    // Verify database counts (should create another guest and another folio)
    $this->assertDatabaseCount('guests', 2);
    $this->assertDatabaseCount('folios', 2);

    // Get the new guest and folio
    $newGuest = Guest::where('guest_id', '!=', $existingGuest->guest_id)->first();
    $newFolio = Folio::where('guest_id', $newGuest->guest_id)->first();

    $this->assertEquals('Alice', $newGuest->first_name);
    $this->assertEquals('Smith', $newGuest->last_name);
    $this->assertEquals('OPEN', $newFolio->status);
    $this->assertEquals(2000.00, $newFolio->net_rate);

    // Booking created for new folio
    $this->assertDatabaseHas('bookings', [
        'folio_id' => $newFolio->folio_id,
        'room_id' => $this->room->room_id,
        'status' => 'CHECKED_IN',
    ]);

    // Check auto-posted room charge on the new folio
    $this->assertDatabaseHas('transactions', [
        'folio_id' => $newFolio->folio_id,
        'charge_code' => 100,
        'charge_amount' => 2000.00,
    ]);
});

test('creating a reservation for an existing guest reuses their guest record and generates a new isolated folio', function (): void {
    // Pre-create existing guest and folio
    $guest = Guest::create([
        'first_name' => 'Bob',
        'last_name' => 'Builder',
        'contact_number' => '09111111111',
    ]);

    $folio = Folio::create([
        'folio_number' => 'REG-2026999',
        'registration_number' => 'REGNUM-2026999',
        'guest_id' => $guest->guest_id,
        'status' => 'CLOSED',
        'net_rate' => 1200.00,
    ]);

    // Create reservation with same name but new unique folio details
    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.reservation.store'), [
            'first_name' => 'Bob',
            'last_name' => 'Builder',
            'contact_number' => '09111111111',
            'address_line1' => 'Bob Town',
            'folio_number' => 'REG-2026998', // new unique folio number
            'registration_number' => 'REGNUM-2026998',
            'room_id' => $this->room->room_id,
            'arrival_date' => now()->toDateString(),
            'arrival_time' => '12:00',
            'departure_date' => now()->addDays(1)->toDateString(),
            'departure_time' => '12:00',
            'num_pax' => 1,
        ]);

    $response->assertRedirect(route('frontdesk.reservation'));
    $response->assertSessionHas('success');

    // Verify database counts (guest is reused, folio is newly created)
    $this->assertDatabaseCount('guests', 1); // Only Bob
    $this->assertDatabaseCount('folios', 2); // REG-2026999 and REG-2026998

    $newFolio = Folio::where('folio_number', 'REG-2026998')->first();
    $this->assertNotNull($newFolio);
    $this->assertEquals('OPEN', $newFolio->status);

    // Old folio remains CLOSED
    $this->assertEquals('CLOSED', $folio->refresh()->status);

    // Booking created with status RESERVED linked to new folio
    $this->assertDatabaseHas('bookings', [
        'folio_id' => $newFolio->folio_id,
        'room_id' => $this->room->room_id,
        'status' => 'RESERVED',
    ]);

    // Check that room charges are NOT posted yet since it is only a RESERVATION
    $this->assertDatabaseMissing('transactions', [
        'folio_id' => $newFolio->folio_id,
        'charge_code' => 100,
    ]);
});
