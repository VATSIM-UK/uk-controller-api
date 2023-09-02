<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;

class AirlineAircraftArrivalStandAllocator implements ArrivalStandAllocator
{
    use SelectsFromSizeAppropriateAvailableStands;
    use SelectsFirstApplicableStand;
    use OrdersStandsByCommonConditions;
    use AppliesOrdering;

    private const ORDER_BYS = [
        'airline_stand.priority ASC',
    ];

    /**
     * This allocator:
     * 
     * - Selects stands that are size appropriate and available
     * - Filters these to stands that are specifically selected for the airline AND a given aircraft type
     * - Orders these stands by the airline's priority for the stand
     * - Orders these stands by the common conditions, minus the general allocation priority
     * (see OrdersStandsByCommonConditions)
     * - Selects the first stand that pops up
     */
    public function allocate(NetworkAircraft $aircraft): ?int
    {
        // We cant allocate a stand if we don't know the airline or aircraft type
        if ($aircraft->airline_id === null || $aircraft->aircraft_id === null) {
            return null;
        }


        return $this->selectFirstStand(
            $this->applyOrderingToStandsQuery(
                $this->joinOtherStandRequests(
                    $this->sizeAppropriateAvailableStandsAtAirfield($aircraft)
                        ->airline($aircraft->airline_id)
                        ->where('airline_stand.aircraft_id', $aircraft->aircraft_id),
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
