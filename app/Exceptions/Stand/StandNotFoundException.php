<?php

namespace App\Exceptions\Stand;

use InvalidArgumentException;

class StandNotFoundException extends InvalidArgumentException
{
    public static function forId(int $standId): StandNotFoundException
    {
        return new static(sprintf('Stand with id %d not found', $standId));
    }
}
