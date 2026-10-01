<?php

use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->facilityPerm = Permission::firstOrCreate(
        ['permission_key' => 'manage-facilities'],
        [
            'description' => 'Manage facilities',
            'module' => 'Front Desk',
            'is_active' => true,
        ]
    );

    $this->reservationPerm = Permission::firstOrCreate(
        ['permission_key' => 'manage-reservations'],
        [
            'description' => 'Manage reservations',
            'module' => 'Front Desk',
            'is_active' => true,
        ]
    );

    $this->role = Role::firstOrCreate(
        ['role_name' => 'FRONT_DESK'],
        ['description' => 'Front Desk Role', 'is_active' => true]
    );

    $this->role->permissions()->syncWithoutDetaching([
        $this->facilityPerm->permission_id,
        $this->reservationPerm->permission_id,
    ]);

    $this->user = User::factory()->create([
        'role_id' => $this->role->role_id,
        'is_active' => true,
    ]);
});

test('user with manage-facilities can view facilities list', function (): void {
    Facility::create([
        'name' => 'Basketball Gym',
        'rate' => 1000,
        'rate_type' => 'hourly',
        'capacity' => 100,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->get(route('frontdesk.facilities.index'));

    $response->assertStatus(200);
    $response->assertSee('Basketball Gym');
    $response->assertSee('Total Facilities');
});

test('user can create a facility from frontdesk', function (): void {
    Storage::fake('public');

    $response = $this->actingAs($this->user)->post(route('frontdesk.facilities.store'), [
        'name' => 'Grand Function Hall',
        'description' => 'Ideal for weddings and banquets.',
        'capacity' => 300,
        'rate' => 15000,
        'rate_type' => 'daily',
        'is_active' => 1,
        'sort_order' => 1,
    ]);

    $response->assertRedirect(route('frontdesk.facilities.index'));
    $response->assertSessionHas('success');

    $facility = Facility::where('name', 'Grand Function Hall')->first();
    expect($facility)->not->toBeNull();
    expect($facility->rate_type)->toBe('daily');
    expect((float) $facility->rate)->toBe(15000.0);
    expect($facility->capacity)->toBe(300);
});

test('user can edit a facility from frontdesk', function (): void {
    $facility = Facility::create([
        'name' => 'Conference Room A',
        'rate' => 1200,
        'rate_type' => 'hourly',
        'capacity' => 20,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->put(route('frontdesk.facilities.update', $facility), [
        'name' => 'Conference Room A (Renovated)',
        'rate' => 1500,
        'rate_type' => 'hourly',
        'capacity' => 25,
        'is_active' => 1,
        'sort_order' => 2,
    ]);

    $response->assertRedirect(route('frontdesk.facilities.index'));
    $facility->refresh();
    expect($facility->name)->toBe('Conference Room A (Renovated)');
    expect((float) $facility->rate)->toBe(1500.0);
});

test('user can toggle active status of a facility', function (): void {
    $facility = Facility::create([
        'name' => 'Outdoor Stage',
        'rate' => 8000,
        'rate_type' => 'daily',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->patch(route('frontdesk.facilities.toggle', $facility));

    $response->assertRedirect(route('frontdesk.facilities.index'));
    $facility->refresh();
    expect($facility->is_active)->toBeFalse();
});

test('user can delete a facility from frontdesk', function (): void {
    $facility = Facility::create([
        'name' => 'Temporary Tent',
        'rate' => 500,
        'rate_type' => 'hourly',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->delete(route('frontdesk.facilities.destroy', $facility));

    $response->assertRedirect(route('frontdesk.facilities.index'));
    expect(Facility::find($facility->facility_id))->toBeNull();
});

test('frontdesk dashboard displays facilities section and schedule', function (): void {
    $facility = Facility::create([
        'name' => 'Tennis Court',
        'rate' => 300,
        'rate_type' => 'hourly',
        'capacity' => 4,
        'is_active' => true,
    ]);

    FacilityReservation::create([
        'facility_id' => $facility->facility_id,
        'booker_name' => 'Maria Santos',
        'booker_email' => 'maria@example.com',
        'booker_contact' => '09123456789',
        'reservation_date' => now()->toDateString(),
        'start_time' => '10:00:00',
        'end_time' => '12:00:00',
        'duration_hours' => 2,
        'estimated_amount' => 600,
        'status' => 'approved',
        'terms_accepted' => true,
    ]);

    $response = $this->actingAs($this->user)->get(route('frontdesk.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Hotel Room Monitoring & Calendar', false);
    $response->assertSee('Facilities');
    $response->assertSee('Tennis Court');
    $response->assertSee('Maria Santos');
});

test('front desk can book a facility reservation', function (): void {
    $facility = Facility::create([
        'name' => 'Badminton Court 1',
        'rate' => 200,
        'rate_type' => 'hourly',
        'capacity' => 4,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->post(route('frontdesk.facility-reservations.store'), [
        'facility_id' => $facility->facility_id,
        'booker_name' => 'Carlos Yulo',
        'booker_email' => 'carlos@example.com',
        'booker_contact' => '09171234567',
        'reservation_date' => now()->addDay()->toDateString(),
        'start_time' => '14:00',
        'end_time' => '16:00',
        'status' => 'approved',
    ]);

    $reservation = FacilityReservation::where('booker_name', 'Carlos Yulo')->first();
    expect($reservation)->not->toBeNull();
    expect($reservation->status)->toBe('approved');
    expect((float) $reservation->estimated_amount)->toBe(400.0);
    $response->assertRedirect(route('frontdesk.facility-reservations.show', $reservation));
});

test('front desk staff without manage-facilities cannot create facility', function (): void {
    $this->role->permissions()->detach($this->facilityPerm->permission_id);

    $frontDeskOnlyUser = User::factory()->create([
        'role_id' => $this->role->role_id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($frontDeskOnlyUser)->get(route('frontdesk.facilities.index'));
    $response->assertForbidden();

    $responseCreate = $this->actingAs($frontDeskOnlyUser)->get(route('frontdesk.facilities.create'));
    $responseCreate->assertForbidden();
});

test('front desk can book facility reservation with agreed rate, event details, and multi-day schedule', function (): void {
    $facility = Facility::create([
        'name' => 'Grand Conference Hall',
        'rate' => 1000,
        'rate_type' => 'hourly',
        'hourly_rate' => 1000,
        'daily_rate' => 8000,
        'capacity' => 100,
        'is_active' => true,
    ]);

    $startDate = now()->addDays(2)->toDateString();
    $endDate = now()->addDays(4)->toDateString(); // 3 days

    // Book 3 days, 8:00 to 17:00 (9 hours per day), with agreed rate of 800/hr (overriding 1000/hr)
    $response = $this->actingAs($this->user)->post(route('frontdesk.facility-reservations.store'), [
        'facility_id' => $facility->facility_id,
        'booker_name' => 'Prof. Santos',
        'booker_email' => 'santos@evsu.edu.ph',
        'booker_contact' => '09189876543',
        'event_name' => 'Regional Academic Summit',
        'event_details' => 'Full sound setup, 3 microphone stands, rostrum',
        'billing_type' => 'hourly',
        'reservation_date' => $startDate,
        'end_date' => $endDate,
        'start_time' => '08:00',
        'end_time' => '17:00',
        'agreed_rate' => 800.00,
        'status' => 'approved',
    ]);

    $reservation = FacilityReservation::where('booker_name', 'Prof. Santos')->first();
    expect($reservation)->not->toBeNull();
    expect($reservation->event_name)->toBe('Regional Academic Summit');
    expect($reservation->event_details)->toContain('Full sound setup');
    expect($reservation->total_days)->toBe(3);
    expect((float) $reservation->agreed_rate)->toBe(800.0);
    // 9 hours/day * 3 days * 800/hr = 21,600
    expect((float) $reservation->estimated_amount)->toBe(21600.0);
    $response->assertRedirect(route('frontdesk.facility-reservations.show', $reservation));
});

test('front desk can choose daily billing mode for reservation', function (): void {
    $facility = Facility::create([
        'name' => 'Audio Visual Room',
        'rate' => 500,
        'rate_type' => 'hourly',
        'hourly_rate' => 500,
        'daily_rate' => 3500,
        'capacity' => 50,
        'is_active' => true,
    ]);

    $startDate = now()->addDays(5)->toDateString();
    $endDate = now()->addDays(6)->toDateString(); // 2 days

    $response = $this->actingAs($this->user)->post(route('frontdesk.facility-reservations.store'), [
        'facility_id' => $facility->facility_id,
        'booker_name' => 'Dean Dela Cruz',
        'booker_email' => 'dean@evsu.edu.ph',
        'booker_contact' => '09121112233',
        'billing_type' => 'daily',
        'reservation_date' => $startDate,
        'end_date' => $endDate,
        'start_time' => '08:00',
        'end_time' => '17:00',
        'status' => 'approved',
    ]);

    $reservation = FacilityReservation::where('booker_name', 'Dean Dela Cruz')->first();
    expect($reservation)->not->toBeNull();
    expect($reservation->billing_type)->toBe('daily');
    expect($reservation->total_days)->toBe(2);
    // 2 days * 3500/day = 7000
    expect((float) $reservation->estimated_amount)->toBe(7000.0);
});

test('front desk can operate facility controls: check-in, extend, and timeout with excess calculation', function (): void {
    $facility = Facility::create([
        'name' => 'Seminar Hall',
        'rate' => 300,
        'rate_type' => 'hourly',
        'hourly_rate' => 300,
        'is_active' => true,
    ]);

    $reservation = FacilityReservation::create([
        'facility_id' => $facility->facility_id,
        'booker_name' => 'Engr. Rivera',
        'booker_email' => 'rivera@example.com',
        'booker_contact' => '09201234567',
        'billing_type' => 'hourly',
        'reservation_date' => now()->toDateString(),
        'end_date' => now()->toDateString(),
        'total_days' => 1,
        'start_time' => '09:00:00',
        'end_time' => '12:00:00',
        'duration_hours' => 3.0,
        'estimated_amount' => 900.0,
        'status' => 'approved',
        'terms_accepted' => true,
    ]);

    // 1. Check-In
    $checkInResponse = $this->actingAs($this->user)
        ->patch(route('frontdesk.facility-reservations.check-in', $reservation));
    $checkInResponse->assertRedirect();
    $reservation->refresh();
    expect($reservation->status)->toBe('active');
    expect($reservation->actual_start_time)->not->toBeNull();

    // 2. Extend reservation by 1 hour (end_time becomes 13:00)
    $extendResponse = $this->actingAs($this->user)
        ->patch(route('frontdesk.facility-reservations.extend', $reservation), [
            'end_date' => now()->toDateString(),
            'end_time' => '13:00',
        ]);
    $extendResponse->assertRedirect();
    $reservation->refresh();
    expect(substr($reservation->end_time, 0, 5))->toBe('13:00');
    expect((float) $reservation->duration_hours)->toBe(4.0);
    expect((float) $reservation->estimated_amount)->toBe(1200.0);

    // 3. Time-Out (Complete)
    $timeoutResponse = $this->actingAs($this->user)
        ->patch(route('frontdesk.facility-reservations.time-out', $reservation));
    $timeoutResponse->assertRedirect();
    $reservation->refresh();
    expect($reservation->status)->toBe('completed');
    expect($reservation->actual_end_time)->not->toBeNull();
    expect($reservation->final_amount)->not->toBeNull();
});

test('front desk can cancel facility reservation', function (): void {
    $facility = Facility::create([
        'name' => 'Board Room',
        'rate' => 500,
        'rate_type' => 'hourly',
        'is_active' => true,
    ]);

    $reservation = FacilityReservation::create([
        'facility_id' => $facility->facility_id,
        'booker_name' => 'Mrs. Tan',
        'booker_email' => 'tan@example.com',
        'booker_contact' => '09391234567',
        'reservation_date' => now()->addDay()->toDateString(),
        'end_date' => now()->addDay()->toDateString(),
        'total_days' => 1,
        'start_time' => '10:00:00',
        'end_time' => '12:00:00',
        'duration_hours' => 2.0,
        'estimated_amount' => 1000.0,
        'status' => 'approved',
        'terms_accepted' => true,
    ]);

    $response = $this->actingAs($this->user)
        ->patch(route('frontdesk.facility-reservations.cancel', $reservation), [
            'cancellation_notes' => 'Event postponed indefinitely by organizers.',
        ]);

    $response->assertRedirect();
    $reservation->refresh();
    expect($reservation->status)->toBe('cancelled');
    expect($reservation->admin_notes)->toContain('Event postponed indefinitely');
});
