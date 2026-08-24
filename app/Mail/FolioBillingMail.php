<?php

namespace App\Mail;

use App\Models\Folio;
use App\Models\SystemSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class FolioBillingMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Folio $folio) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->resolveSender(),
            subject: "Folio Statement & Billing Summary - #{$this->folio->folio_number}",
        );
    }

    public function content(): Content
    {
        // Load relationships needed for summary
        $this->folio->load(['guest', 'transactions.chargeCode', 'bookings.room']);

        return new Content(
            view: 'emails.folio-email-body',
            with: [
                'folio' => $this->folio,
                'guest' => $this->folio->guest,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => Pdf::loadView('emails.folio-billing', [
                'folio' => $this->folio,
            ])->output(), "Folio-{$this->folio->folio_number}.pdf")
                ->withMime('application/pdf'),
        ];
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
