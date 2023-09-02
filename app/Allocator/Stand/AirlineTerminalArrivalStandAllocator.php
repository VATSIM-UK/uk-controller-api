<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;

class AirlineTerminalArrivalStandAllocator implements ArrivalStandAllocator
{
    use SelectsStandsFromAirlineSpecificTerminals;

    /**
     * This allocator:
     * 
     * - Selects stands that are size appropriate and available
     * - Filters these to stands that are at terminals specifically selected for the airline
     * - Filters stands to those that dont have specific conditions
     * - Orders these stands by the airline's priority for the stand
     * - Orders these stands by the common conditions, minus the general allocation priority
     * (see OrdersStandsByCommonConditions)
     * - Selects the first stand that pops up
     */
    public function allocate(NetworkAircraft $aircraft): ?int
    {
        // If the aircraft doesnt have an airline, we cant allocate a stand
        if ($aircraft->airline_id === null) {
            return null;
        }

        return $this->selectStandsAtAirlineSpecificTerminals(
            $aircraft,
            fn(Builder $query) => $query->whereNull('airline_terminal.destination')
                ->whereNull('airline_terminal.callsign_slug')
                ->whereNull('airline_terminal.full_callsign')
                ->whereNull('airline_terminal.aircraft_id')
        );
    }
}
