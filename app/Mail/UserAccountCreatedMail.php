<?php

namespace App\Mail;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserAccountCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $plainPassword
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->resolveSender(),
            subject: 'Welcome to EVSU Ormoc - Hotel - Your Account Details',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-account-created',
            with: [
                'user' => $this->user,
                'plainPassword' => $this->plainPassword,
            ],
        );
    }

    protected function resolveSender(): ?Address
    {
        $currentUser = auth()->user();
        if ($currentUser && ! empty($currentUser->email) && filter_var($currentUser->email, FILTER_VALIDATE_EMAIL)) {
            return new Address($currentUser->email, $currentUser->full_name);
        }

        $frontdeskEmail = SystemSetting::get('frontdesk_email');
        if (! empty($frontdeskEmail) && filter_var($frontdeskEmail, FILTER_VALIDATE_EMAIL)) {
            return new Address($frontdeskEmail, 'Front Desk - EVSU Ormoc - Hotel');
        }

        return null;
    }
}
