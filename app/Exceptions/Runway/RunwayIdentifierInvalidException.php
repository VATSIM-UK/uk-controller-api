<?php

namespace App\Exceptions\Runway;

class RunwayIdentifierInvalidException extends RunwayInvalidException
{
    public static function forIdentifier(string $identifier): RunwayIdentifierInvalidException
    {
        return new static(sprintf('Runway identifier %s is not valid', $identifier));
    }
}
