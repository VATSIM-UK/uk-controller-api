<?php

namespace App\Exceptions\Runway;

class RunwayThresholdInvalidException extends RunwayInvalidException
{
    public static function forIdentifier(string $identifier, string $reason): RunwayThresholdInvalidException
    {
        return new static(sprintf('Runway threshold for %s is invalid: %s', $identifier, $reason));
    }
}
