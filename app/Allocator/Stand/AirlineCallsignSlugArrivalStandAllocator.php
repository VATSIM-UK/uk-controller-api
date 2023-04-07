<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;

class AirlineCallsignSlugArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    use UsesCallsignSlugs;

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
            ->airlineCallsign($airline, $this->getCallsignSlugs($aircraft))
            ->orderByRaw('airline_stand.callsign_slug IS NOT NULL')
            ->orderByRaw('LENGTH(airline_stand.callsign_slug) DESC')
            ->orderBy('airline_stand.priority');
    }
}
