<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;

class AirlineCallsignSlugTerminalArrivalStandAllocator implements ArrivalStandAllocator
{
    use AppliesOrdering;
    use UsesCallsignSlugs;
    use SelectsFirstApplicableStand;
    use SelectsStandsFromAirlineTerminals;
    use SelectsFromSizeAppropriateAvailableStands;
    use OrdersStandsByCommonConditions;

    private const ORDER_BYS = [
        'airline_terminal.callsign_slug IS NOT NULL',
        'LENGTH(airline_terminal.callsign_slug) DESC',
        'airline_terminal.priority',
    ];

    private AirlineService $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    /**
     * This allocator:
     * 
     * - Selects stands that are size appropriate and available
     * - Filters these to stands at a terminal that is specifically selected for the airline and
     * a set of callsign slugs
     * - Orders these by the specific callsign slug, descending by length
     * - Orders these stands by the airline's priority for the stand
     * - Orders these stands by the common conditions, minus the general allocation priority
     * (see OrdersStandsByCommonConditions)
     * - Selects the first stand that pops up
     */
    public function allocate(NetworkAircraft $aircraft): ?int {
        // If the aircraft doesnt have an airline, we cant allocate a stand
        if ($aircraft->airline_id === null) {
            return null;
        }

        return $this->selectFirstStand(
            $this->applyOrderingToStandsQuery(
                $this->joinOtherStandRequests(
                    $this->standsAtAirlineTerminals(
                        $this->sizeAppropriateAvailableStandsAtAirfield($aircraft)
                            ->whereIn('airline_terminal.callsign_slug', $this->getCallsignSlugs($aircraft)),
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
