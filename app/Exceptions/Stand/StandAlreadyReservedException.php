<?php

namespace App\Exceptions\Stand;

use InvalidArgumentException;

class StandAlreadyReservedException extends InvalidArgumentException
{
    public static function forId(int $standId): StandAlreadyReservedException
    {
        return new static(sprintf('Stand id %d is already reserved at the given times', $standId));
    }
}
