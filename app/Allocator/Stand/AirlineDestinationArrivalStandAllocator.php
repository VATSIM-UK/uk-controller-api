<?php

namespace App\Allocator\Stand;

use App\Allocator\UsesDestinationStrings;
use App\Models\Vatsim\NetworkAircraft;

class AirlineDestinationArrivalStandAllocator implements ArrivalStandAllocatorInterface
{
    use UsesDestinationStrings;
    use OrdersStandsByCommonConditions;
    use SelectsFirstApplicableStand;
    use SelectsFromSizeAppropriateAvailableStands;
    use AppliesOrdering;

    private const ORDER_BYS = [
        'airline_stand.destination IS NOT NULL',
        'LENGTH(airline_stand.destination) DESC',
        'airline_stand.priority ASC',
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
        if ($aircraft->airline_id === null) {
            return null;
        }

        return $this->selectFirstStand(
            $this->applyOrderingToStandsQuery(
                $this->joinOtherStandRequests(
                    $this->sizeAppropriateAvailableStandsAtAirfield($aircraft)
                        ->with('airlines')
                        ->airlineDestination(
                            $aircraft->airline_id,
                            $this->getDestinationStrings($aircraft)
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
