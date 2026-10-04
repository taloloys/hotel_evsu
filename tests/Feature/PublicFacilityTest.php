<?php

use App\Models\Facility;
use App\Models\FacilityReservation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public can view facility details page with updated layout and pricing', function (): void {
    $facility = Facility::create([
        'name' => 'Badminton Court',
        'description' => 'A multipurpose sports court.',
        'capacity' => 20,
        'rate' => 250.00,
        'rate_type' => 'hourly',
        'is_active' => true,
    ]);

    $response = $this->get(route('facilities.show', $facility));

    $response->assertStatus(200);
    $response->assertSee('Badminton Court');
    $response->assertDontSee('Badminton Court (EVSU)');
    $response->assertSee('₱250.00');
    $response->assertSee('/ HR');
    $response->assertSee('Up to 20 Persons');
    $response->assertSee('Description');
    $response->assertDontSee('Included Amenities & Services');
    $response->assertSee('Ready to Book?');
    $response->assertSee('No Upcoming Reservations');
    $response->assertSee('Available for Booking');
});

test('public facility details displays approved upcoming reservations', function (): void {
    $facility = Facility::create([
        'name' => 'Function Hall',
        'description' => 'Spacious hall for seminars.',
        'capacity' => 100,
        'rate' => 5000.00,
        'rate_type' => 'daily',
        'is_active' => true,
    ]);

    FacilityReservation::create([
        'facility_id' => $facility->facility_id,
        'booker_name' => 'Juan Dela Cruz',
        'booker_email' => 'juan@example.com',
        'booker_contact' => '09123456789',
        'reservation_date' => now()->addDays(2)->toDateString(),
        'start_time' => '08:00:00',
        'end_time' => '12:00:00',
        'duration_hours' => 4,
        'estimated_amount' => 5000.00,
        'status' => 'approved',
        'terms_accepted' => true,
        'terms_accepted_at' => now(),
    ]);

    $response = $this->get(route('facilities.show', $facility));

    $response->assertStatus(200);
    $response->assertSee('Upcoming Reservations');
    $response->assertSee('Reserved');
});

test('booking form pre-populates date and time parameters from facility details planner', function (): void {
    $facility = Facility::create([
        'name' => 'Court (EVSU)',
        'description' => 'Court facility',
        'capacity' => 20,
        'rate' => 250.00,
        'rate_type' => 'hourly',
        'is_active' => true,
    ]);

    $targetDate = now()->addDays(5)->format('Y-m-d');

    $response = $this->get(route('facilities.book', [
        'facility' => $facility,
        'date' => $targetDate,
        'start_time' => '10:00',
        'end_time' => '14:00',
    ]));

    $response->assertStatus(200);
    $response->assertSee('value="'.$targetDate.'"', false);
    $response->assertSee("startTime: '10:00'", false);
    $response->assertSee('Terms & Conditions');
});

test('facility show page contains single and multiple days booking switcher', function (): void {
    $facility = Facility::create([
        'name' => 'Conference Room',
        'description' => 'A meeting room.',
        'capacity' => 15,
        'rate' => 300.00,
        'rate_type' => 'hourly',
        'is_active' => true,
    ]);

    $response = $this->get(route('facilities.show', $facility));

    $response->assertStatus(200);
    $response->assertSee('Single Day');
    $response->assertSee('Multiple Days');
    $response->assertSee('setBookingMode');
    $response->assertSee('bookingMode');
});

test('booking form pre-populates multi-day date range and event information', function (): void {
    $facility = Facility::create([
        'name' => 'Auditorium',
        'description' => 'Large auditorium',
        'capacity' => 500,
        'rate' => 1000.00,
        'hourly_rate' => 1000.00,
        'daily_rate' => 8000.00,
        'rate_type' => 'daily',
        'is_active' => true,
    ]);

    $startDate = now()->addDays(3)->format('Y-m-d');
    $endDate = now()->addDays(6)->format('Y-m-d');

    $response = $this->get(route('facilities.book', [
        'facility' => $facility,
        'date' => $startDate,
        'end_date' => $endDate,
        'start_time' => '08:00',
        'end_time' => '17:00',
        'billing_type' => 'daily',
        'event_name' => 'Regional Hackathon 2026',
        'event_details' => 'Auditorium setup for 300 participants',
    ]));

    $response->assertStatus(200);
    $response->assertSee('value="'.$startDate.'"', false);
    $response->assertSee('value="'.$endDate.'"', false);
    $response->assertSee('Regional Hackathon 2026');
    $response->assertSee('Auditorium setup for 300 participants');
    $response->assertSee("endDate: '{$endDate}'", false);
});

test('booking page contains terms agreement gate elements and hidden input in form', function (): void {
    $facility = Facility::create([
        'name' => 'Executive Conference Room',
        'description' => 'Conference room',
        'capacity' => 20,
        'rate' => 1200.00,
        'hourly_rate' => 1200.00,
        'rate_type' => 'hourly',
        'is_active' => true,
    ]);

    $response = $this->get(route('facilities.book', $facility));

    $response->assertStatus(200);
    $response->assertSee(':disabled="!scrolledToBottom"', false);
    $response->assertSee('terms-container');
    $response->assertSee('<input type="hidden" name="terms_accepted" value="1">', false);
    $response->assertSee('Scroll down inside the terms box to unlock the agreement');
});

test('facility booking submission requires terms_accepted', function (): void {
    $facility = Facility::create([
        'name' => 'Meeting Room',
        'description' => 'Meeting room',
        'capacity' => 10,
        'rate' => 500.00,
        'hourly_rate' => 500.00,
        'rate_type' => 'hourly',
        'is_active' => true,
    ]);

    $response = $this->from(route('facilities.book', $facility))->post(route('facilities.submit', $facility), [
        'booker_name' => 'Maria Clara',
        'booker_email' => 'maria@example.com',
        'booker_contact' => '09123456789',
        'reservation_date' => now()->addDays(2)->format('Y-m-d'),
        'start_time' => '08:00',
        'end_time' => '12:00',
        'billing_type' => 'hourly',
    ]);

    $response->assertSessionHasErrors(['terms_accepted']);
    $this->assertDatabaseCount('facility_reservations', 0);
});

test('facility booking submission succeeds when terms_accepted is provided', function (): void {
    $facility = Facility::create([
        'name' => 'Meeting Room',
        'description' => 'Meeting room',
        'capacity' => 10,
        'rate' => 500.00,
        'hourly_rate' => 500.00,
        'rate_type' => 'hourly',
        'is_active' => true,
    ]);

    $date = now()->addDays(2)->format('Y-m-d');
    $response = $this->post(route('facilities.submit', $facility), [
        'booker_name' => 'Maria Clara',
        'booker_email' => 'maria@example.com',
        'booker_contact' => '09123456789',
        'event_name' => 'Strategic Planning',
        'event_details' => 'Annual strategic planning meeting',
        'reservation_date' => $date,
        'start_time' => '08:00',
        'end_time' => '12:00',
        'billing_type' => 'hourly',
        'terms_accepted' => '1',
    ]);

    $reservation = FacilityReservation::first();
    expect($reservation)->not->toBeNull()
        ->and($reservation->booker_name)->toBe('Maria Clara')
        ->and($reservation->terms_accepted)->toBeTrue()
        ->and($reservation->status)->toBe('pending');

    $response->assertRedirect(route('facilities.confirmation', $reservation));
});
