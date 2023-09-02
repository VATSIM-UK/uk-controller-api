<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;

class AirlineTerminalArrivalStandAllocator implements ArrivalStandAllocator
{
    use AppliesOrdering;
    use SelectsFirstApplicableStand;
    use SelectsStandsFromAirlineTerminals;
    use SelectsFromSizeAppropriateAvailableStands;
    use OrdersStandsByCommonConditions;

    private const ORDER_BYS = [
        'airline_terminal.priority ASC',
    ];

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

        return $this->selectFirstStand(
            $this->applyOrderingToStandsQuery(
                $this->joinOtherStandRequests(
                    $this->standsAtAirlineTerminals(
                        $this->sizeAppropriateAvailableStandsAtAirfield($aircraft)
                            ->whereNull('airline_terminal.destination')
                            ->whereNull('airline_terminal.callsign_slug')
                            ->whereNull('airline_terminal.full_callsign')
                            ->whereNull('airline_terminal.aircraft_id'),
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
