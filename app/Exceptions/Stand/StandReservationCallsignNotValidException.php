<?php

namespace App\Exceptions\Stand;

use InvalidArgumentException;

class StandReservationCallsignNotValidException extends InvalidArgumentException
{
    public static function forCallsign(string $callsign): StandReservationCallsignNotValidException
    {
        return new static(sprintf('Callsign %s is not valid for stand reservation', $callsign));
    }
}
