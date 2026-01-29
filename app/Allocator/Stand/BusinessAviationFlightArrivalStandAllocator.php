<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * The primary arrival stand allocator for business aviation. Looks for a business aviation
 * aircraft in the flightplan. If this is satisfied, it will look
 * for any business aviation stand the airline uses and allocate that.
 */
class BusinessAviationFlightArrivalStandAllocator implements ArrivalStandAllocator
{
    use SelectsFromAirlineSpecificStands;
    use ChecksForBusinessAviationAircraft;

    public function allocate(NetworkAircraft $aircraft): ?int
    {
        // If the aircraft isn't a business aviation aircraft, this rule doesn't apply
        if (!$this->isBusinessAviationAircraft($aircraft)) {
            return null;
        }

        return $this->selectStandsUsingStandardConditions(
            $aircraft,
            $this->queryFilter()
        );
    }

    public function getRankedStandAllocation(NetworkAircraft $aircraft): Collection
    {
        // If the aircraft is unknown, we can't do the ranking
        if (!$aircraft->aircraft) {
            return collect();
        }

        // If the aircraft isn't a business aviation aircraft, we cant allocate a stand
        if (!$this->isBusinessAviationAircraft($aircraft)) {
            return collect();
        }
        
        return $this->selectRankedStandsUsingStandardConditions(
            $aircraft,
            $this->queryFilter()
        );
    }

    private function queryFilter(): Closure
    {
        return fn (Builder $query) => $query->businessAviation();
    }
}
