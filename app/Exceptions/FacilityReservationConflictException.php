<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Collection;

class FacilityReservationConflictException extends Exception
{
    public function __construct(
        string $message = 'One or more facilities conflict with existing reservations.',
        public ?Collection $conflictedFacilities = null,
        int $code = 409
    ) {
        parent::__construct($message, $code);
    }
}
