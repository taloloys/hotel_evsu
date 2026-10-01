<?php

use App\Exceptions\FacilityReservationConflictException;
use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\FacilitySet;
use App\Services\FacilityBookingService;
use App\Services\ReservationReferenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create member facilities
    $this->hallA = Facility::create([
        'name' => 'Function Hall A',
        'capacity' => 150,
        'rate' => 8000.00,
        'rate_type' => 'daily',
        'hourly_rate' => 800.00,
        'daily_rate' => 8000.00,
        'is_active' => true,
    ]);

    $this->hallB = Facility::create([
        'name' => 'Function Hall B',
        'capacity' => 150,
        'rate' => 8000.00,
        'rate_type' => 'daily',
        'hourly_rate' => 800.00,
        'daily_rate' => 8000.00,
        'is_active' => true,
    ]);

    // Create consolidated set: Function Hall
    $this->functionHallSet = FacilitySet::create([
        'name' => 'Function Hall',
        'prefix_code' => 'EVSUOCFH',
        'description' => 'Combined function hall set.',
        'capacity' => 300,
        'hourly_rate' => 1500.00,
        'daily_rate' => 15000.00,
        'rate' => 15000.00,
        'rate_type' => 'daily',
        'is_active' => true,
    ]);

    $this->functionHallSet->facilities()->attach([
        $this->hallA->facility_id,
        $this->hallB->facility_id,
    ]);

    $this->bookingService = app(FacilityBookingService::class);
    $this->referenceService = app(ReservationReferenceService::class);
});

test('Scenario 1: individual facility reservation only makes that facility unavailable', function () {
    $date = now()->addDays(5)->toDateString();

    // Reserve Function Hall A
    $reservation = $this->bookingService->createReservation([
        'booker_name' => 'Alice Johnson',
        'booker_email' => 'alice@example.com',
        'booker_contact' => '09111111111',
        'billing_type' => 'hourly',
        'reservation_date' => $date,
        'end_date' => $date,
        'start_time' => '09:00:00',
        'end_time' => '17:00:00',
        'estimated_amount' => 6400.00,
        'status' => 'approved',
    ], $this->hallA);

    expect($reservation->facility_id)->toBe($this->hallA->facility_id)
        ->and($reservation->facility_set_id)->toBeNull();

    // Check conflict on Hall A: must be conflicting
    $conflictA = $this->bookingService->hasConflict(
        $this->hallA->facility_id,
        $date,
        '10:00:00',
        '12:00:00'
    );
    expect($conflictA)->toBeTrue();

    // Check conflict on Hall B: must remain completely AVAILABLE
    $conflictB = $this->bookingService->hasConflict(
        $this->hallB->facility_id,
        $date,
        '10:00:00',
        '12:00:00'
    );
    expect($conflictB)->toBeFalse();
});

test('Scenario 2: consolidated facility reservation reserves all member facilities under one reference', function () {
    $date = now()->addDays(6)->toDateString();

    // Reserve Function Hall Set
    $reservation = $this->bookingService->createReservation([
        'booker_name' => 'Corporate Client',
        'booker_email' => 'corp@example.com',
        'booker_contact' => '09222222222',
        'billing_type' => 'daily',
        'reservation_date' => $date,
        'end_date' => $date,
        'start_time' => '08:00:00',
        'end_time' => '18:00:00',
        'estimated_amount' => 15000.00,
        'status' => 'approved',
    ], $this->functionHallSet);

    // Only ONE reservation record is created
    expect(FacilityReservation::count())->toBe(1)
        ->and($reservation->isConsolidated())->toBeTrue()
        ->and($reservation->facility_set_id)->toBe($this->functionHallSet->facility_set_id)
        ->and($reservation->reference_number)->toContain('#EVSUOCFH-');

    // Both facilities are linked to this reservation in the pivot table
    $linkedFacilityIds = $reservation->reservedFacilities()->pluck('facilities.facility_id')->all();
    expect($linkedFacilityIds)->toContain($this->hallA->facility_id)
        ->and($linkedFacilityIds)->toContain($this->hallB->facility_id);

    // Both facilities become unavailable for that date and time
    expect($this->bookingService->hasConflict($this->hallA->facility_id, $date, '10:00:00', '12:00:00'))->toBeTrue();
    expect($this->bookingService->hasConflict($this->hallB->facility_id, $date, '10:00:00', '12:00:00'))->toBeTrue();
});

test('Scenario 3: partial conflict rejects consolidated reservation without partial booking', function () {
    $date = now()->addDays(7)->toDateString();

    // Hall B is already reserved
    $this->bookingService->createReservation([
        'booker_name' => 'Prior Booker',
        'booker_email' => 'prior@example.com',
        'booker_contact' => '09333333333',
        'billing_type' => 'hourly',
        'reservation_date' => $date,
        'end_date' => $date,
        'start_time' => '09:00:00',
        'end_time' => '17:00:00',
        'estimated_amount' => 6400.00,
        'status' => 'approved',
    ], $this->hallB);

    $initialCount = FacilityReservation::count();
    expect($initialCount)->toBe(1);

    // User attempts to reserve the Function Hall Set
    $exceptionThrown = false;
    try {
        $this->bookingService->createReservation([
            'booker_name' => 'Set Booker',
            'booker_email' => 'set@example.com',
            'booker_contact' => '09444444444',
            'billing_type' => 'daily',
            'reservation_date' => $date,
            'end_date' => $date,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'estimated_amount' => 15000.00,
            'status' => 'approved',
        ], $this->functionHallSet);
    } catch (FacilityReservationConflictException $e) {
        $exceptionThrown = true;
        expect($e->getMessage())->toContain('This facility set is unavailable for the selected date and time because one or more included facilities have already been reserved.');
    }

    expect($exceptionThrown)->toBeTrue();
    // Verify no partial reservation was created
    expect(FacilityReservation::count())->toBe(1);
    // Hall A was NOT reserved
    expect($this->bookingService->hasConflict($this->hallA->facility_id, $date, '09:00:00', '17:00:00'))->toBeFalse();
});

test('Scenario 4: existing individual reservation conflicts with consolidated set reservation', function () {
    $date = now()->addDays(8)->toDateString();

    // Reserve Function Hall A
    $this->bookingService->createReservation([
        'booker_name' => 'Individual User',
        'booker_email' => 'user@example.com',
        'booker_contact' => '09555555555',
        'billing_type' => 'hourly',
        'reservation_date' => $date,
        'end_date' => $date,
        'start_time' => '13:00:00',
        'end_time' => '17:00:00',
        'estimated_amount' => 3200.00,
        'status' => 'approved',
    ], $this->hallA);

    // Set conflict check must report conflict
    $setHasConflict = $this->bookingService->hasSetConflict(
        $this->functionHallSet->facility_set_id,
        $date,
        '14:00:00',
        '16:00:00'
    );

    expect($setHasConflict)->toBeTrue();
});

test('Scenario 5: consolidated set reservation conflicts with individual facility reservation', function () {
    $date = now()->addDays(9)->toDateString();

    // Reserve Function Hall Set
    $this->bookingService->createReservation([
        'booker_name' => 'Convention Client',
        'booker_email' => 'convention@example.com',
        'booker_contact' => '09666666666',
        'billing_type' => 'daily',
        'reservation_date' => $date,
        'end_date' => $date,
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
        'estimated_amount' => 15000.00,
        'status' => 'approved',
    ], $this->functionHallSet);

    // Attempting to reserve Function Hall B individually must be recognized as in conflict
    $conflictB = $this->bookingService->hasConflict(
        $this->hallB->facility_id,
        $date,
        '09:00:00',
        '12:00:00'
    );

    expect($conflictB)->toBeTrue();
});

test('Scenario 6: daily reference sequence numbers sequentially on the same reservation date', function () {
    $reservationDate = '2026-10-10';

    $ref1 = $this->referenceService->generateReference($this->functionHallSet, $reservationDate);
    $ref2 = $this->referenceService->generateReference($this->functionHallSet, $reservationDate);
    $ref3 = $this->referenceService->generateReference($this->functionHallSet, $reservationDate);

    expect($ref1)->toBe('#EVSUOCFH-20261010-001')
        ->and($ref2)->toBe('#EVSUOCFH-20261010-002')
        ->and($ref3)->toBe('#EVSUOCFH-20261010-003');
});

test('Scenario 7: sequence resets on the next reservation date', function () {
    $day1 = '2026-10-10';
    $day2 = '2026-10-11';

    $refDay1 = $this->referenceService->generateReference($this->functionHallSet, $day1);
    $refDay2 = $this->referenceService->generateReference($this->functionHallSet, $day2);

    expect($refDay1)->toBe('#EVSUOCFH-20261010-001')
        ->and($refDay2)->toBe('#EVSUOCFH-20261011-001');
});

test('Scenario 8: consolidated reservation generates ONE reference number associated with all member facilities', function () {
    $date = '2026-10-15';

    $res = $this->bookingService->createReservation([
        'booker_name' => 'Summit Organizers',
        'booker_email' => 'summit@example.com',
        'booker_contact' => '09777777777',
        'billing_type' => 'daily',
        'reservation_date' => $date,
        'end_date' => $date,
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
        'estimated_amount' => 15000.00,
        'status' => 'approved',
    ], $this->functionHallSet);

    // One reference number generated
    expect($res->reference_number)->toBe('#EVSUOCFH-20261015-001');

    // Both facilities are linked to this exact reference number
    $facilities = $res->reservedFacilities;
    expect($facilities->count())->toBe(2);

    $hallAReservations = $this->hallA->reservedReservations()->pluck('reference_number')->all();
    $hallBReservations = $this->hallB->reservedReservations()->pluck('reference_number')->all();

    expect($hallAReservations)->toContain('#EVSUOCFH-20261015-001')
        ->and($hallBReservations)->toContain('#EVSUOCFH-20261015-001');
});

test('Scenario 9: concurrent reservations maintain atomicity and avoid duplicate references or double-bookings', function () {
    $date = '2026-10-20';

    // Transaction 1 books Function Hall Set
    $res1 = $this->bookingService->createReservation([
        'booker_name' => 'User One',
        'booker_email' => 'user1@example.com',
        'booker_contact' => '09888888881',
        'billing_type' => 'hourly',
        'reservation_date' => $date,
        'end_date' => $date,
        'start_time' => '10:00:00',
        'end_time' => '14:00:00',
        'estimated_amount' => 6000.00,
        'status' => 'approved',
    ], $this->functionHallSet);

    expect($res1->reference_number)->toBe('#EVSUOCFH-20261020-001');

    // User Two tries simultaneously to reserve Function Hall A for the same time
    $failed = false;
    try {
        $this->bookingService->createReservation([
            'booker_name' => 'User Two',
            'booker_email' => 'user2@example.com',
            'booker_contact' => '09888888882',
            'billing_type' => 'hourly',
            'reservation_date' => $date,
            'end_date' => $date,
            'start_time' => '10:00:00',
            'end_time' => '14:00:00',
            'estimated_amount' => 3200.00,
            'status' => 'approved',
        ], $this->hallA);
    } catch (FacilityReservationConflictException $e) {
        $failed = true;
    }

    expect($failed)->toBeTrue();
    expect(FacilityReservation::count())->toBe(1);

    // User Three books a non-conflicting afternoon slot for Function Hall Set
    $res3 = $this->bookingService->createReservation([
        'booker_name' => 'User Three',
        'booker_email' => 'user3@example.com',
        'booker_contact' => '09888888883',
        'billing_type' => 'hourly',
        'reservation_date' => $date,
        'end_date' => $date,
        'start_time' => '14:00:00',
        'end_time' => '18:00:00',
        'estimated_amount' => 6000.00,
        'status' => 'approved',
    ], $this->functionHallSet);

    // Guaranteed distinct, incremented reference number
    expect($res3->reference_number)->toBe('#EVSUOCFH-20261020-002');
    expect(FacilityReservation::count())->toBe(2);
});

test('Scenario 10: existing reservations retain old reference format, participate in availability, and remain intact', function () {
    // Manually insert an old-style reservation with FACIL-XXXXXX format
    $oldRef = 'FACIL-000042';
    $oldDate = now()->addDays(3)->toDateString();

    $oldReservation = FacilityReservation::withoutEvents(function () use ($oldRef, $oldDate) {
        return FacilityReservation::create([
            'facility_id' => $this->hallA->facility_id,
            'booker_name' => 'Historical Booker',
            'booker_email' => 'historical@example.com',
            'booker_contact' => '09999999999',
            'reservation_date' => $oldDate,
            'end_date' => $oldDate,
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'duration_hours' => 4.0,
            'estimated_amount' => 3200.00,
            'status' => 'approved',
            'reference_number' => $oldRef,
            'terms_accepted' => true,
        ]);
    });

    // Populate pivot as migration backfill does
    $oldReservation->reservedFacilities()->sync([$this->hallA->facility_id]);

    // Retains original reference number
    expect($oldReservation->fresh()->reference_number)->toBe('FACIL-000042');

    // Participates in availability check: Function Hall Set cannot be booked during that time
    expect($this->bookingService->hasSetConflict($this->functionHallSet->facility_set_id, $oldDate, '08:00:00', '12:00:00'))->toBeTrue();

    // But non-overlapping time on that same day is still available
    expect($this->bookingService->hasSetConflict($this->functionHallSet->facility_set_id, $oldDate, '13:00:00', '17:00:00'))->toBeFalse();
});
