<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AirlineCallsignArrivalStandAllocator implements ArrivalStandAllocator, RankableArrivalStandAllocator
{
    use SelectsFromAirlineSpecificStands;
    use UsesCallsignSlugs;

    private readonly AirlineService $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    /**
     * This allocator:
     * 
     * - Selects stands that are size appropriate and available
     * - Filters these to stands that are specifically selected for the airline and a specific callsign
     * - Orders these stands by the airline's priority for the stand
     * - Orders these stands by the common conditions, minus the general allocation priority
     * (see OrdersStandsByCommonConditions)
     * - Selects the first stand that pops up
     */
    public function allocate(NetworkAircraft $aircraft): ?int
    {
        // We can only allocate a stand if we know the airline
        if ($aircraft->airline_id === null) {
            return null;
        }

        return $this->selectAirlineSpecificStands(
            $aircraft,
            $this->queryFilter($aircraft)
        );
    }

    public function getRankedStandAllocation(NetworkAircraft $aircraft): Collection
    {
        // We can only allocate a stand if we know the airline
        if ($aircraft->airline_id === null) {
            return collect();
        }

        return $this->selectRankedAirlineSpecificStands(
            $aircraft,
            $this->queryFilter($aircraft)
        );
    }

    private function queryFilter(NetworkAircraft $aircraft): Closure
    {
        return fn(Builder $query) =>
            $query->where('airline_stand.full_callsign', $this->getFullCallsignSlug($aircraft));
    }
}
