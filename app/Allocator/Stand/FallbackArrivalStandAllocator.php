<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;

class FallbackArrivalStandAllocator implements ArrivalStandAllocator
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
        return $this->selectStandsUsingStandardConditions(
            $aircraft,
            fn(Builder $query) => $query->notCargo(),
            $this->commonOrderByConditions
        );
    }
}
