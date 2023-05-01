<?php

namespace App\Allocator\Stand;

use App\Models\Aircraft\Aircraft;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;

class AirlineAircraftArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    private AirlineService $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    protected function getOrderedStandsQuery(Builder $stands, NetworkAircraft $aircraft): ?Builder
    {
        $airline = $this->airlineService->getAirlineForAircraft($aircraft);
        if ($airline === null) {
            return null;
        }

        $aircraftType = Aircraft::where('code', $aircraft->planned_aircraft)->first();
        if (!$aircraftType) {
            return null;
        }

        return $stands->with('airlines')
            ->airline($airline)
            ->where('airline_stand.aircraft_id', $aircraftType->id)
            ->orderBy('airline_stand.priority');
    }
}
