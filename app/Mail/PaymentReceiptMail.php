<?php

namespace App\Mail;

use App\Models\SystemSetting;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Transaction $transaction) {}

    public function envelope(): Envelope
    {
        $ref = $this->transaction->charge_number ?? "TXN-{$this->transaction->transaction_id}";

        return new Envelope(
            from: $this->resolveSender(),
            subject: "Payment Receipt - {$ref}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-receipt',
            with: [
                'transaction' => $this->transaction,
                'folio' => $this->transaction->folio,
                'guest' => $this->transaction->folio?->guest,
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
