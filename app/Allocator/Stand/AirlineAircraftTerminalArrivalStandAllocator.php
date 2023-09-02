<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;

class AirlineAircraftTerminalArrivalStandAllocator implements ArrivalStandAllocator
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
            fn(Builder $query) => $query->where('airline_terminal.aircraft_id', $aircraft->aircraft_id)
        );
    }
}
