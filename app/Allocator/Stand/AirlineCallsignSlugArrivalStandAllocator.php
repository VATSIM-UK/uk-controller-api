<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class AirlineCallsignSlugArrivalStandAllocator extends AbstractArrivalStandAllocator
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

        return $stands->with('airlines')
            ->airlineCallsign($airline, $this->getCallsignSlugs($aircraft))
            ->orderByRaw('airline_stand.callsign_slug IS NOT NULL')
            ->orderByRaw('LENGTH(airline_stand.callsign_slug) DESC')
            ->orderBy('airline_stand.priority');
    }

    public function getCallsignSlugs(NetworkAircraft $aircraft): array
    {
        $slug = $this->airlineService->getCallsignSlugForAircraft($aircraft);
        $slugs = [];
        for ($i = 0; $i < Str::length($slug); $i++) {
            $slugs[] = Str::substr($slug, 0, $i + 1);
        }

        return $slugs;
    }
}
