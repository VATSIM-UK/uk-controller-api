<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;

class AirlineArrivalStandAllocator implements ArrivalStandAllocator
{
    use AppliesOrdering;
    use SelectsFirstApplicableStand;
    use SelectsFromSizeAppropriateAvailableStands;
    use OrdersStandsByCommonConditions;

    private const ORDER_BYS = [
        'airline_stand.priority ASC',
    ];

    /**
     * This allocator:
     * 
     * - Selects stands that are size appropriate and available
     * - Filters these to stands that are specifically selected for the airline and do not have any specific conditions
     * - Orders these stands by the airline's priority for the stand
     * - Orders these stands by the common conditions, minus the general allocation priority
     * (see OrdersStandsByCommonConditions)
     * - Selects the first stand that pops up
     */
    public function allocate(NetworkAircraft $aircraft): ?int
    {
        // We can only allocate a stand if we know the airline
        if ($aircraft->airline_id === null) {
            return null;
        }


        return $this->selectFirstStand(
            $this->applyOrderingToStandsQuery(
                $this->joinOtherStandRequests(
                    $this->sizeAppropriateAvailableStandsAtAirfield($aircraft)
                        ->airline($aircraft->airline_id)
                        ->whereNull('airline_stand.destination')
                        ->whereNull('airline_stand.callsign_slug')
                        ->whereNull('airline_stand.full_callsign')
                        ->whereNull('airline_stand.aircraft_id'),
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
