<?php

namespace App\Allocator\Stand;

use App\Allocator\UsesDestinationStrings;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;

class AirlineDestinationTerminalArrivalStandAllocator implements ArrivalStandAllocator
{
    use UsesDestinationStrings;
    use SelectsStandsFromAirlineSpecificTerminals;

    private const ORDER_BYS = [
        'airline_terminal.destination IS NOT NULL',
        'LENGTH(airline_terminal.destination) DESC',
    ];

    /**
     * This allocator:
     * 
     * - Selects stands that are size appropriate and available
     * - Filters these to stands that are at terminals specifically selected for the airline and a
     * specific set of destinations
     * - Orders these by the most specific destination first
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
            fn(Builder $query) => $query->whereIn(
                'airline_terminal.destination',
                $this->getDestinationStrings($aircraft)
            ),
            self::ORDER_BYS
        );
    }
}
