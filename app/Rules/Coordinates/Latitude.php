<?php

namespace App\Rules\Coordinates;

use Illuminate\Contracts\Validation\Rule;

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
