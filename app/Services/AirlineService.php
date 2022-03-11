<?php

namespace App\Services;

use App\Models\Airline\Airline;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Str;

class AirlineService
{
    public function getAirlineForAircraft(NetworkAircraft $aircraft): ?Airline
    {
        return Airline::where('icao_code', Str::substr($aircraft->callsign, 0, 3))->first();
    }

    public function getCallsignSlugForAircraft(NetworkAircraft $aircraft): string
    {
        $airline = $this->getAirlineForAircraft($aircraft);
        return $airline
            ? Str::substr($aircraft->callsign, 3)
            : $aircraft->callsign;
    }
}
