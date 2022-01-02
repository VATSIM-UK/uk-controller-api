<?php

namespace App\Exceptions\Stand;

use InvalidArgumentException;

class StandReservationCidNotValidException extends InvalidArgumentException
{
    public static function forCid(int $cid): StandReservationCidNotValidException
    {
        return new static(sprintf('Vatsim CID %d is not valid for stand reservation', $cid));
    }
}
