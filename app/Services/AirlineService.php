<?php

namespace App\Services;

use App\Models\Airline\Airline;
use App\Models\Vatsim\NetworkAircraft;

class AirlineService
{
    public function getAirlineForAircraft(NetworkAircraft $aircraft): ?Airline
    {
        return Airline::where('icao_code', substr($aircraft->callsign, 0, 3))->first();
    }
}
