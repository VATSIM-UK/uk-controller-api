<?php

namespace App\Rules\Coordinates;

use App\Rules\Coordinates\Coordinate;

class Longitude extends Coordinate
{
    protected function maximumAllowedValue(): float
    {
        return 180.0;
    }

    public function getTypeForMessage(): string
    {
        return "longitude";
    }
}
