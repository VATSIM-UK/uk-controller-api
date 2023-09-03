<?php

namespace App\Allocator\Stand;

use App\Allocator\UsesDestinationStrings;
use App\Models\Vatsim\NetworkAircraft;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AirlineDestinationArrivalStandAllocator implements ArrivalStandAllocator
{
    use UsesDestinationStrings;
    use SelectsFromAirlineSpecificStands;

    private const ORDER_BYS = [
        'airline_stand.destination IS NOT NULL',
        'LENGTH(airline_stand.destination) DESC',
    ];

    /**
     * This allocator:
     * 
     * - Selects stands that are size appropriate and available
     * - Filters these to stands that are specifically selected for the airline and a specific set of destinations
     * - Orders these by the most specific destination first
     * - Orders these stands by the airline's priority for the stand
     * - Orders these stands by the common conditions, minus the general allocation priority
     * (see OrdersStandsByCommonConditions)
     * - Selects the first stand that pops up
     */
    public function allocate(NetworkAircraft $aircraft): ?int
    {
        // We cant allocate a stand if we don't know the airline
        if ($aircraft->airline_id === null || $aircraft->aircraft_id === null) {
            return null;
        }

        return $this->selectAirlineSpecificStands(
            $aircraft,
            $this->queryFilter($aircraft),
            self::ORDER_BYS
        );
    }

    public function getRankedStandAllocation(NetworkAircraft $aircraft): Collection
    {
        // We can only allocate a stand if we know the airline
        if ($aircraft->airline_id === null || $aircraft->aircraft_id === null) {
            return collect();
        }

        return $this->selectRankedAirlineSpecificStands(
            $aircraft,
            $this->queryFilter($aircraft),
            self::ORDER_BYS
        );
    }

    private function queryFilter(NetworkAircraft $aircraft): Closure
    {
        return fn(Builder $query) => $query->whereIn(
            'airline_stand.destination',
            $this->getDestinationStrings($aircraft)
        );
    }
}
