<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FallbackArrivalStandAllocator implements ArrivalStandAllocator, RankableArrivalStandAllocator
{
    use SelectsStandsUsingStandardConditions;

    /**
     * This allocator:
     * 
     * - Only allocates stands that are not cargo
     * - Orders by common conditions (see OrdersStandsByCommonConditions)
     * - Selects the first available stand (see SelectsFirstApplicableStand)
     *
     * @param NetworkAircraft $aircraft
     * @return integer|null
     */
    public function allocate(NetworkAircraft $aircraft): ?int
    {
        if ($aircraft->aircraft_id === null) {
            return null;
        }

        return $this->selectStandsUsingStandardConditions(
            $aircraft,
            $this->filterQuery(),
        );
    }

    public function getRankedStandAllocation(NetworkAircraft $aircraft): Collection
    {
        if ($aircraft->aircraft_id === null) {
            return collect();
        }

        return $this->selectRankedStandsUsingStandardConditions(
            $aircraft,
            $this->filterQuery(),
        );
    }

    private function filterQuery(): Closure
    {
        return fn(Builder $query) => $query->notCargo();
    }
}
