<?php

namespace App\Rules\Coordinates;

class Latitude extends Coordinate
{
    protected function maximumAllowedValue(): float
    {
        return 90.0;
    }

    public function getTypeForMessage(): string
    {
        return "latitude";
    }
}
