<?php

namespace App\Filament\Helpers;

use App\Models\Airfield\Airfield;
use App\Models\Airline\Airline;
use App\Models\Controller\ControllerPosition;

class SelectOptions
{
    public static function airfields()
    {
        return Airfield::all()->mapWithKeys(fn (Airfield $airfield) => [$airfield->id => $airfield->code]);
    }

    public static function airlines()
    {
        return Airline::all()->mapWithKeys(fn (Airline $airline) => [$airline->id => $airline->icao_code]);
    }

    public static function controllers()
    {
        return ControllerPosition::all()->mapWithKeys(
            fn (ControllerPosition $controller) => [$controller->id => $controller->callsign]
        );
    }
}
