<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;

class AirlineArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    private AirlineService $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    protected function getOrderedStandsQuery(Builder $stands, NetworkAircraft $aircraft): ?Builder
    {
        $airline = $this->airlineService->getAirlineForAircraft($aircraft);
        return $airline === null
            ? null
            : $stands->airline($airline)
                ->whereNull('airline_stand.destination')
                ->whereNull('airline_stand.callsign_slug')
                ->whereNull('airline_stand.full_callsign')
                ->whereNull('airline_stand.aircraft_id')
                ->orderBy('airline_stand.priority');
    }
}
