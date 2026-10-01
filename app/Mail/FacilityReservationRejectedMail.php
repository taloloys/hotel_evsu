<?php

namespace App\Mail;

use App\Models\FacilityReservation;
use App\Models\SystemSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FacilityReservationRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public FacilityReservation $reservation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [new Address($this->reservation->booker_email, $this->reservation->booker_name)],
            from: $this->resolveSender(),
            subject: "Facility Reservation Update — Ref #{$this->reservation->reference_number}",
        );
    }

    public function content(): Content
    {
        $this->reservation->load(['facility', 'facilitySet', 'reservedFacilities']);

        return new Content(
            view: 'emails.facility-reservation-rejected',
            with: [
                'reservation' => $this->reservation,
                'facility' => $this->reservation->facility ?? $this->reservation->facilitySet,
            ],
        );
    }

    protected function resolveSender(): ?Address
    {
        $user = auth()->user();
        if ($user && ! empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            return new Address($user->email, $user->full_name);
        }

        $frontdeskEmail = SystemSetting::get('frontdesk_email');
        if (! empty($frontdeskEmail) && filter_var($frontdeskEmail, FILTER_VALIDATE_EMAIL)) {
            return new Address($frontdeskEmail, 'Front Desk - Don Felipe Hotel');
        }

        return null;
    }
}
