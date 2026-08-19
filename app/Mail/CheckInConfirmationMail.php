<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\SystemSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CheckInConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        $roomNumber = $this->booking->room?->room_number ?? 'N/A';

        return new Envelope(
            from: $this->resolveSender(),
            subject: "Welcome to Don Felipe Hotel - Checked In (Room {$roomNumber})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.check-in-confirmation',
            with: [
                'booking' => $this->booking,
                'folio' => $this->booking->folio,
                'guest' => $this->booking->folio?->guest,
                'room' => $this->booking->room,
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
