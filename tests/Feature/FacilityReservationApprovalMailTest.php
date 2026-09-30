<?php

use App\Mail\FacilityReservationApprovedMail;
use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\FacilityBookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Mail::fake();

    $this->permission = Permission::firstOrCreate([
        'permission_key' => 'frontdesk-manage-facility-reservations',
        'description' => 'Manage facility reservations',
        'module' => 'Frontdesk',
        'is_active' => true,
    ]);

    $this->role = Role::firstOrCreate(
        ['role_name' => 'SUPER_ADMIN'],
        ['description' => 'Super Administrator', 'is_system_admin' => true, 'is_active' => true]
    );

    $this->user = User::factory()->create([
        'role_id' => $this->role->role_id,
        'email' => 'staff@evsu.edu.ph',
    ]);

    $this->facility = Facility::create([
        'name' => 'Conference Hall A',
        'rate' => 1000,
        'rate_type' => 'hourly',
        'capacity' => 80,
        'is_active' => true,
    ]);

    $this->reservation = FacilityReservation::create([
        'facility_id' => $this->facility->facility_id,
        'booker_name' => 'Maria Santos',
        'booker_email' => 'maria.santos@example.com',
        'booker_contact' => '+63 912 345 6789',
        'reservation_date' => now()->addDays(2)->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '12:00:00',
        'duration_hours' => 3,
        'estimated_amount' => 3000,
        'status' => 'pending',
        'terms_accepted' => true,
        'terms_accepted_at' => now(),
    ]);
});

test('approving facility reservation queues FacilityReservationApprovedMail to reservee with automated notice', function (): void {
    $response = $this->actingAs($this->user)
        ->patch(route('frontdesk.facility-reservations.approve', $this->reservation));

    $response->assertRedirect(route('frontdesk.facility-reservations.index'));
    $response->assertSessionHas('success');

    $this->reservation->refresh();
    expect($this->reservation->status)->toBe('approved');

    Mail::assertQueued(FacilityReservationApprovedMail::class, function (FacilityReservationApprovedMail $mail): bool {
        return $mail->hasTo('maria.santos@example.com')
            && $mail->reservation->reservation_id === $this->reservation->reservation_id;
    });
});

test('FacilityReservationApprovedMail renders expected details and automated disclaimer', function (): void {
    $mailable = new FacilityReservationApprovedMail($this->reservation);
    $html = $mailable->render();

    expect($html)->toContain('Maria Santos')
        ->toContain($this->reservation->reference_number)
        ->toContain('Conference Hall A')
        ->toContain('3,000.00')
        ->toContain('This is an automated system email. Please do not reply directly.');
});

test('FacilityBookingService calculates strictly positive duration and amount', function (): void {
    $service = app(FacilityBookingService::class);

    $duration = $service->computeDurationHours('03:40', '04:40');
    expect($duration)->toBe(1.0);

    $amount = $service->calculateAmount($this->facility, '03:40', '04:40');
    expect($amount)->toBe(1000.0);

    // Also with seconds format H:i:s
    $durationWithSeconds = $service->computeDurationHours('03:40:00', '04:40:00');
    expect($durationWithSeconds)->toBe(1.0);
});
