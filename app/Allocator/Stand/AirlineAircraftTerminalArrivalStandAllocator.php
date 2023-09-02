<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;

class AirlineAircraftTerminalArrivalStandAllocator implements ArrivalStandAllocator
{
    use OrdersStandsByCommonConditions;
    use SelectsFirstApplicableStand;
    use SelectsFromSizeAppropriateAvailableStands;
    use SelectsStandsFromAirlineTerminals;
    use AppliesOrdering;

    private const ORDER_BYS = [
        'airline_terminal.priority ASC',
    ];

    /**
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

        return $this->selectFirstStand(
            $this->applyOrderingToStandsQuery(
                $this->joinOtherStandRequests(
                    $this->standsAtAirlineTerminals(
                        $this->sizeAppropriateAvailableStandsAtAirfield($aircraft)
                            ->where('airline_terminal.aircraft_id', $aircraft->aircraft_id),
                        $aircraft
                    ),
                    $aircraft
                ),
                array_merge(
                    self::ORDER_BYS,
                    $this->commonOrderByConditionsWithoutAssignmentPriority
                )
            )
        );
    }
}
