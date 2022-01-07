<?php

namespace App\Exceptions\Airfield;

use InvalidArgumentException;

class AirfieldNotFoundException extends InvalidArgumentException
{
    public static function fromIcao(string $icao): AirfieldNotFoundException
    {
        return new static(sprintf('Airfield with icao %s not found', $icao));
    }
}
