<?php

namespace App\Allocator\Stand;

use App\Allocator\UsesDestinationStrings;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;

class AirlineDestinationArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    use UsesDestinationStrings;

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

        return $stands->with('airlines')
            ->airlineDestination($airline, $this->getDestinationStrings($aircraft))
            ->orderByRaw('airline_stand.destination IS NOT NULL')
            ->orderByRaw('LENGTH(airline_stand.destination) DESC')
            ->orderBy('airline_stand.priority');
    }
}
