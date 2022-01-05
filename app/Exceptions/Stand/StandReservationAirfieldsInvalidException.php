<?php

namespace App\Exceptions\Stand;

use InvalidArgumentException;

class StandReservationAirfieldsInvalidException extends InvalidArgumentException
{
    public static function forOrigin(string $airfield): StandReservationAirfieldsInvalidException
    {
        return new static(sprintf('Stand reservation origin airfield %s is invalid', $airfield));
    }

    public static function forDestination(string $airfield): StandReservationAirfieldsInvalidException
    {
        return new static(sprintf('Stand reservation destination airfield %s is invalid', $airfield));
    }

    public static function forBoth(): StandReservationAirfieldsInvalidException
    {
        return new static('Stand reservations require both or neither airfield to be set');
    }
}
