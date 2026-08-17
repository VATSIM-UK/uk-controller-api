<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AirlineAircraftTerminalArrivalStandAllocator implements ArrivalStandAllocator, RankableArrivalStandAllocator
{
    use SelectsStandsFromAirlineSpecificTerminals;

    /*
     * This allocator:
     *
     * - Selects stands that are size appropriate and available
     * - Filters these to stands at terminals that are specifically selected for the airline AND a given aircraft type
     * - Orders these stands by the airline's priority for the stand
     * - Orders these stands by the common conditions, minus the general allocation priority
     * (see OrdersStandsByCommonConditions)
     * - Selects the first stand that pops up
     */
    public function allocate(NetworkAircraft $aircraft): ?int
    {
        // We can only allocate a stand if we know the airline and aircraft type
        if ($aircraft->airline_id === null || $aircraft->aircraft_id === null) {
            return null;
        }

        return $this->selectStandsAtAirlineSpecificTerminals(
            $aircraft,
            $this->queryFilter($aircraft)
        );
    }

    public function getRankedStandAllocation(NetworkAircraft $aircraft): Collection
    {
        // We cant allocate a stand if we don't know the airline or aircraft type
        if ($aircraft->airline_id === null || $aircraft->aircraft_id === null) {
            return collect();
        }

        return $this->selectRankedStandsAtAirlineSpecificTerminals(
            $aircraft,
            $this->queryFilter($aircraft)
        );
    }

    private function queryFilter(NetworkAircraft $aircraft): Closure
    {
        return fn (Builder $query) => $query->where('airline_terminal.aircraft_id', $aircraft->aircraft_id);
    }
}
