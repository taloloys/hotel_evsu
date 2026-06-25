<?php

use App\Models\ChargeCode;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Room;
use App\Models\Shift;
use App\Models\Transaction;
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
});

test('staff can search guests via AJAX', function (): void {
    $response = $this->actingAs($this->frontdeskUser)
        ->get(route('frontdesk.guests.search', ['q' => 'John']));

    $response->assertOk();
    $response->assertJsonFragment([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
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

    // Verify auto-posted room charges night-by-night (only Night 1 posted at first)
    $this->assertDatabaseHas('transactions', [
        'charge_code' => 100,
        'charge_amount' => 2000.00,
        'reference_notes' => 'Room charge for Night 1 (Date: '.now()->toDateString().')',
    ]);

    $this->assertDatabaseMissing('transactions', [
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
        'charge_code' => 100,
        'charge_amount' => 2000.00,
        'reference_notes' => 'Room charge for Night 2 (Date: '.now()->toDateString().')',
    ]);
});

test('authenticated staff can check in existing guest and generate a new isolated folio', function (): void {
    // Pre-create ChargeCode 100
    ChargeCode::create([
        'charge_code' => 100,
        'description' => 'ROOM CHARGE',
        'category' => 'HOTEL',
        'is_active' => true,
    ]);

    // Pre-create an existing folio for this guest representing a previous stay
    $existingFolio = Folio::create([
        'folio_number' => 'REG-9999001',
        'registration_number' => 'REGNUM-9999001',
        'guest_id' => $this->guest->guest_id,
        'status' => 'CLOSED',
        'net_rate' => 1500.00,
    ]);

    // Ensure a shift exists for the user first
    $shift = Shift::create([
        'user_id' => $this->frontdeskUser->user_id,
        'start_time' => now(),
    ]);

    // Pre-create a transaction on the past stay's folio to verify isolation
    Transaction::create([
        'folio_id' => $existingFolio->folio_id,
        'charge_code' => 100, // ROOM CHARGE
        'shift_id' => $shift->shift_id,
        'user_id' => $this->frontdeskUser->user_id,
        'transaction_date' => now()->subDays(5)->toDateString(),
        'charge_number' => 'RM-OLD-1',
        'payment_method' => 'NONE',
        'reference_notes' => 'Old Room Charge',
        'charge_amount' => 1500.00,
        'credit_amount' => 0.00,
    ]);

    // Check in the guest for a new stay, generating a brand-new unique folio number
    $response = $this->actingAs($this->frontdeskUser)
        ->post(route('frontdesk.checkin.store'), [
            'guest_id' => $this->guest->guest_id,
            'folio_number' => 'REG-9999002', // new stay's unique folio number
            'registration_number' => 'REGNUM-9999002',
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

    // Confirm that a brand-new, second folio was created (not reusing the old one)
    $this->assertDatabaseCount('folios', 2);

    $newFolio = Folio::where('folio_number', 'REG-9999002')->first();
    $this->assertNotNull($newFolio);
    $this->assertEquals('OPEN', $newFolio->status);
    $this->assertEquals(2000.00, $newFolio->net_rate); // Room base rate is 2000

    $this->assertDatabaseHas('bookings', [
        'folio_id' => $newFolio->folio_id,
        'room_id' => $this->room->room_id,
        'status' => 'CHECKED_IN',
    ]);

    // Check that the new room charge was posted to the new folio
    $this->assertDatabaseHas('transactions', [
        'folio_id' => $newFolio->folio_id,
        'charge_code' => 100,
        'charge_amount' => 2000.00,
    ]);

    // Confirm isolation: the new folio does not have the old transaction, and the old folio does not have the new transaction
    $this->assertDatabaseMissing('transactions', [
        'folio_id' => $newFolio->folio_id,
        'charge_number' => 'RM-OLD-1',
    ]);

    $this->assertDatabaseMissing('transactions', [
        'folio_id' => $existingFolio->folio_id,
        'charge_amount' => 2000.00,
    ]);
});
