<?php

namespace App\Services;

class LocationService
{
    public static function metersToNauticalMiles(float $meters): float {
        return $meters * 0.000539957;
    }
}
