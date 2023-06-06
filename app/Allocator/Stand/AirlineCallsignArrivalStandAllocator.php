<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;

class AirlineCallsignArrivalStandAllocator extends AbstractArrivalStandAllocator
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
            ->airline($airline)
            ->where('airline_stand.full_callsign', $this->getFullCallsignSlug($aircraft))
            ->orderBy('airline_stand.priority');
    }
}
