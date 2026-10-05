<?php

use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->staffRole = Role::create([
        'role_name' => 'FRONT_DESK',
        'description' => 'Front Desk Role',
        'is_active' => true,
    ]);

    $this->reservationsPermission = Permission::create([
        'permission_key' => 'manage-reservations',
        'description' => 'Manage reservations',
        'module' => 'Front Desk',
        'is_active' => true,
    ]);

    $this->staffRole->permissions()->sync([
        $this->reservationsPermission->permission_id,
    ]);

    $this->staffUser = User::factory()->create([
        'username' => 'frontdesk_monitor_user',
        'full_name' => 'Front Desk Staff',
        'role_id' => $this->staffRole->role_id,
        'is_active' => true,
    ]);
});

test('unauthenticated users cannot access frontdesk monitoring page', function (): void {
    $this->get(route('frontdesk.monitoring'))
        ->assertRedirect('/');
});

test('frontdesk staff can view the monitoring page with room and facility containers', function (): void {
    $facility = Facility::create([
        'name' => 'Grand Conference Ballroom',
        'rate' => 1500,
        'rate_type' => 'hourly',
        'capacity' => 100,
        'is_active' => true,
    ]);

    $room = Room::create([
        'room_number' => '301',
        'room_type' => 'Executive Suite',
        'base_rate' => 3500.00,
        'status' => 'AVAILABLE',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->staffUser)
        ->get(route('frontdesk.monitoring'));

    $response->assertOk();
    $response->assertSee('Hotel Room Monitoring & Calendar', false);
    $response->assertSee('Rooms');
    $response->assertSee('Facilities');
    $response->assertSee('Calendar Timeline');
    $response->assertSee('Room Grid');
    $response->assertSee('Grand Conference Ballroom');
    $response->assertSee('Executive Suite');
    $response->assertSee('data-turbo-cache="false"', false);
    $response->assertSee('<meta name="turbo-cache-control" content="no-cache">', false);
    $response->assertSee('window.fetchRoomTimelineData', false);
    $response->assertSee('window.fetchFacilityTimelineData', false);
    $response->assertSee('window.rejectFacilityReservation', false);
    $response->assertSee('window.closeFacilityReservationModal', false);
    $response->assertDontSee('facilityReservationModal?.hide()', false);
    $response->assertSee('submitFacilityReservationRejection', false);
    $response->assertSee("['admin_notes', reason]", false);

    $html = $response->getContent();
    $panelSwitcherPosition = strpos($html, 'window.switchMonitoringPanel = function(panel)');
    $initializationPosition = strrpos($html, 'initMonitoringPage();');

    expect($panelSwitcherPosition)->not->toBeFalse()
        ->and($initializationPosition)->not->toBeFalse()
        ->and($panelSwitcherPosition)->toBeLessThan($initializationPosition);
});

test('frontdesk staff can fetch facility timeline data', function (): void {
    $facility = Facility::create([
        'name' => 'Pavilion Hall',
        'rate' => 5000,
        'rate_type' => 'daily',
        'capacity' => 200,
        'is_active' => true,
    ]);

    $today = Carbon::today()->toDateString();

    FacilityReservation::create([
        'facility_id' => $facility->facility_id,
        'booker_name' => 'Engr. John Doe',
        'booker_email' => 'john@example.com',
        'booker_contact' => '09123456780',
        'event_name' => 'Annual Engineering Summit',
        'reservation_date' => $today,
        'end_date' => Carbon::today()->addDays(2)->toDateString(),
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
        'billing_type' => 'daily',
        'duration_hours' => 9,
        'estimated_amount' => 15000,
        'status' => 'approved',
        'terms_accepted' => true,
    ]);

    $response = $this->actingAs($this->staffUser)
        ->getJson(route('frontdesk.monitoring.facility-timeline-data'));

    $response->assertOk()
        ->assertJsonStructure([
            'range' => ['start_date', 'end_date', 'days', 'display'],
            'dates',
            'facilities',
            'facility_filters',
            'summary' => ['total', 'available', 'booked', 'in_use', 'reserved', 'pending', 'maintenance'],
        ]);

    $data = $response->json();
    expect($data['dates'])->toBeArray()->and(count($data['dates']))->toBe(14);
    expect($data['facilities'])->toBeArray()->and(count($data['facilities']))->toBe(1);

    $pavilion = $data['facilities'][0];
    expect($pavilion['name'])->toBe('Pavilion Hall');
    expect($pavilion['reservations'])->toBeArray()->and(count($pavilion['reservations']))->toBe(1);
    expect($pavilion['reservations'][0]['booker_name'])->toBe('Engr. John Doe');
    expect($pavilion['reservations'][0]['event_name'])->toBe('Annual Engineering Summit');
});

test('frontdesk staff can reject a pending facility reservation from monitoring', function (): void {
    Mail::fake();

    $facility = Facility::create([
        'name' => 'Executive Conference Room',
        'rate' => 1200,
        'rate_type' => 'hourly',
        'capacity' => 20,
        'is_active' => true,
    ]);

    $reservation = FacilityReservation::create([
        'facility_id' => $facility->facility_id,
        'booker_name' => 'Adrianna Strosin',
        'booker_email' => 'letitia52@example.net',
        'booker_contact' => '09773074402',
        'event_name' => 'Facility Reservation',
        'reservation_date' => Carbon::today()->toDateString(),
        'end_date' => Carbon::today()->toDateString(),
        'start_time' => '13:00:00',
        'end_time' => '16:00:00',
        'billing_type' => 'hourly',
        'duration_hours' => 3,
        'estimated_amount' => 3600,
        'status' => 'pending',
        'terms_accepted' => true,
    ]);

    $response = $this->actingAs($this->staffUser)
        ->patch(route('frontdesk.facility-reservations.reject', $reservation), [
            'admin_notes' => 'Time slot is unavailable.',
            'return_to' => 'monitoring',
        ]);

    $response->assertRedirect(route('frontdesk.monitoring', ['panel' => 'facilities']))
        ->assertSessionHas('success');

    expect($reservation->refresh()->status)->toBe('rejected')
        ->and($reservation->admin_notes)->toBe('Time slot is unavailable.');
});

test('facility details opened from monitoring returns to monitoring', function (): void {
    $facility = Facility::create([
        'name' => 'Executive Conference Room',
        'rate' => 1200,
        'rate_type' => 'hourly',
        'capacity' => 20,
        'is_active' => true,
    ]);

    $reservation = FacilityReservation::create([
        'facility_id' => $facility->facility_id,
        'booker_name' => 'Adrianna Strosin',
        'booker_email' => 'letitia52@example.net',
        'booker_contact' => '09773074402',
        'reservation_date' => Carbon::today()->toDateString(),
        'end_date' => Carbon::today()->toDateString(),
        'start_time' => '13:00:00',
        'end_time' => '16:00:00',
        'billing_type' => 'hourly',
        'duration_hours' => 3,
        'estimated_amount' => 3600,
        'status' => 'pending',
        'terms_accepted' => true,
    ]);

    $response = $this->actingAs($this->staffUser)
        ->get(route('frontdesk.facility-reservations.show', [
            'reservation' => $reservation,
            'return_to' => 'monitoring',
        ]));

    $response->assertOk()
        ->assertSee(route('frontdesk.monitoring', ['panel' => 'facilities']), false)
        ->assertSee('name="return_to" value="monitoring"', false);
});

test('frontdesk staff can fetch room timeline data from monitoring endpoint', function (): void {
    $room = Room::create([
        'room_number' => '401',
        'room_type' => 'Deluxe Room',
        'base_rate' => 2000.00,
        'status' => 'AVAILABLE',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->staffUser)
        ->getJson(route('frontdesk.monitoring.room-timeline-data'));

    $response->assertOk()
        ->assertJsonStructure([
            'range',
            'dates',
            'rooms',
            'room_types',
            'summary',
        ]);

    $data = $response->json();
    expect($data['rooms'])->toBeArray()->and(count($data['rooms']))->toBe(1);
    expect($data['rooms'][0]['room_number'])->toBe('401');
});

test('frontdesk staff can fetch 1-day facility timeline data for hourly view', function (): void {
    $facility = Facility::create([
        'name' => 'Audio Visual Room',
        'rate' => 800,
        'rate_type' => 'hourly',
        'capacity' => 50,
        'is_active' => true,
    ]);

    $today = Carbon::today()->toDateString();

    FacilityReservation::create([
        'facility_id' => $facility->facility_id,
        'booker_name' => 'Dr. Alan Turing',
        'booker_email' => 'alan@example.com',
        'booker_contact' => '09123456789',
        'event_name' => 'Computing Lecture',
        'reservation_date' => $today,
        'start_time' => '09:00:00',
        'end_time' => '12:00:00',
        'billing_type' => 'hourly',
        'duration_hours' => 3,
        'estimated_amount' => 2400,
        'status' => 'approved',
        'terms_accepted' => true,
    ]);

    $response = $this->actingAs($this->staffUser)
        ->getJson(route('frontdesk.monitoring.facility-timeline-data', [
            'start_date' => $today,
            'days' => 1,
        ]));

    $response->assertOk();
    $data = $response->json();
    expect($data['dates'])->toBeArray()->and(count($data['dates']))->toBe(1);
    expect($data['facilities'])->toBeArray()->and(count($data['facilities']))->toBe(1);
    expect($data['facilities'][0]['reservations'])->toBeArray()->and(count($data['facilities'][0]['reservations']))->toBe(1);
    expect($data['facilities'][0]['reservations'][0]['start_time'])->toBe('09:00');
    expect($data['facilities'][0]['reservations'][0]['end_time'])->toBe('12:00');
});

test('facility reservation store redirects back to monitoring when return_to=monitoring', function (): void {
    $facility = Facility::create([
        'name' => 'Boardroom Alpha',
        'rate' => 1000,
        'rate_type' => 'hourly',
        'capacity' => 15,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->staffUser)
        ->post(route('frontdesk.facility-reservations.store'), [
            'facility_id' => $facility->facility_id,
            'booker_name' => 'Maria Santos',
            'booker_email' => 'maria@example.com',
            'booker_contact' => '09123456789',
            'event_name' => 'Quarterly Meeting',
            'reservation_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'billing_type' => 'hourly',
            'status' => 'approved',
            'terms_accepted' => '1',
            'return_to' => 'monitoring',
        ]);

    $response->assertRedirect(route('frontdesk.monitoring', ['panel' => 'facilities']));
    $response->assertSessionHas('success');
});
