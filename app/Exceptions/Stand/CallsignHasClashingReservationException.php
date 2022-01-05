<?php

namespace App\Exceptions\Stand;

use InvalidArgumentException;

class CallsignHasClashingReservationException extends InvalidArgumentException
{
    public static function forCallsign(string $callsign): CallsignHasClashingReservationException
    {
        return new static(sprintf('Callsign %s has a clashing stand reservation', $callsign));
    }
}
