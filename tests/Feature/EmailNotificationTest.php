<?php

use App\Mail\FolioBillingMail;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Role;
use App\Models\User;
use App\Services\EmailRecipientResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();

    $role = Role::firstOrCreate(
        ['role_name' => 'SUPER_ADMIN'],
        ['description' => 'Super Administrator', 'is_system_admin' => true, 'is_active' => true]
    );

    $this->user = User::factory()->create([
        'role_id' => $role->role_id,
        'email' => 'admin@evsu.edu.ph',
    ]);
});

test('email recipient resolver routes directly to guest email when available', function () {
    $guest = Guest::create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);

    $folio = Folio::create([
        'folio_number' => 'TEST-001',
        'guest_id' => $guest->guest_id,
    ]);

    $resolver = app(EmailRecipientResolver::class);
    $recipients = $resolver->resolve('folio', $folio);

    expect($recipients)->toBe(['john.doe@example.com']);
});

test('email recipient resolver falls back to system address if guest email is missing', function () {
    $guest = Guest::create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => null,
    ]);

    $folio = Folio::create([
        'folio_number' => 'TEST-002',
        'guest_id' => $guest->guest_id,
    ]);

    $resolver = app(EmailRecipientResolver::class);
    $recipients = $resolver->resolve('folio', $folio);

    expect($recipients)->toBe([config('mail.from.address')]);
});

test('manual send folio email dispatches FolioBillingMail to guest', function () {
    $guest = Guest::create([
        'first_name' => 'Mark',
        'last_name' => 'Anthony',
        'email' => 'mark@example.com',
    ]);

    $folio = Folio::create([
        'folio_number' => 'TEST-FOLIO-003',
        'guest_id' => $guest->guest_id,
        'status' => 'OPEN',
    ]);

    $response = $this->actingAs($this->user)
        ->post(route('frontdesk.guest-folio.send-email', $folio->folio_id));

    $response->assertRedirect();

    Mail::assertQueued(FolioBillingMail::class, function ($mail) use ($folio) {
        return $mail->folio->folio_id === $folio->folio_id;
    });
});
