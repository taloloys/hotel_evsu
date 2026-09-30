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
