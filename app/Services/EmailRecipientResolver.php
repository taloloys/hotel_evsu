<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Transaction;

class EmailRecipientResolver
{
    /**
     * Resolve recipient email address directly to the Guest / Client.
     *
     * @param  string  $transactionType  'reservation'|'checkin'|'payment'|'folio'
     * @param  mixed|null  $record  Booking|Folio|Transaction|Guest
     */
    public function resolve(string $transactionType, mixed $record = null, ?string $explicitRecipient = null): array
    {
        if (! empty($explicitRecipient) && filter_var($explicitRecipient, FILTER_VALIDATE_EMAIL)) {
            return [$explicitRecipient];
        }

        $guestEmail = $this->extractGuestEmail($record);
        if (! empty($guestEmail) && filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
            return [$guestEmail];
        }

        return [];
    }

    /**
     * Extract guest email from Guest, Folio, Booking, or Transaction model.
     */
    protected function extractGuestEmail(mixed $record): ?string
    {
        if (! $record) {
            return null;
        }

        if ($record instanceof Guest) {
            return $record->email;
        }

        if ($record instanceof Folio) {
            return $record->guest?->email;
        }

        if ($record instanceof Booking) {
            return $record->folio?->guest?->email;
        }

        if ($record instanceof Transaction) {
            return $record->folio?->guest?->email;
        }

        return null;
    }
}
