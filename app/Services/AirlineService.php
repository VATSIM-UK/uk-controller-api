<?php

namespace App\Services;

use App\Models\Airline\Airline;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AirlineService
{
    private const AIRLINE_CODE_ID_CACHE_MAP = 'AIRLINE_CODE_ID_MAP';

    public function getAirlineForAircraft(NetworkAircraft $aircraft): ?Airline
    {
        $airlineId = $this->airlineIdForCallsign($aircraft->callsign);
        return $airlineId
            ? Airline::find($airlineId)
            : null;
    }

    public function airlineIdForCallsign(string $callsign): ?int
    {
        return $this->airlineCodeIdMap()[Str::substr($callsign, 0, 3)] ?? null;
    }

    public function getCallsignSlugForAircraft(NetworkAircraft $aircraft): string
    {
        $airline = $this->getAirlineForAircraft($aircraft);
        return $airline
            ? Str::substr($aircraft->callsign, 3)
            : $aircraft->callsign;
    }

    public function airlinesUpdated()
    {
        Cache::forget(self::AIRLINE_CODE_ID_CACHE_MAP);
    }

    private function airlineCodeIdMap(): array
    {
        return Cache::rememberForever(
            self::AIRLINE_CODE_ID_CACHE_MAP,
            fn() => Airline::all(['id', 'icao_code'])->mapWithKeys(function (Airline $airline)
            {
                return [$airline->icao_code => $airline->id];
            })->toArray()
        );
    }
}
