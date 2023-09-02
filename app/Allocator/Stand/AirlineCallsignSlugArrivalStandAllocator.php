<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;

class AirlineCallsignSlugArrivalStandAllocator implements ArrivalStandAllocator
{
    use AppliesOrdering;
    use UsesCallsignSlugs;
    use SelectsFirstApplicableStand;
    use SelectsFromSizeAppropriateAvailableStands;
    use OrdersStandsByCommonConditions;

    private const ORDER_BYS = [
        'airline_stand.callsign_slug IS NOT NULL',
        'LENGTH(airline_stand.callsign_slug) DESC',
        'airline_stand.priority ASC',
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
     * - Filters these to stands that are specifically selected for the airline and a specific callsign slug
     * - Orders these stands by the airline's priority for the stand
     * - Orders these stands by the common conditions, minus the general allocation priority
     * (see OrdersStandsByCommonConditions)
     * - Selects the first stand that pops up
     */
    public function allocate(NetworkAircraft $aircraft): ?int
    {
        // We can't allocate a stand if we don't know the airline
        if ($aircraft->airline_id === null) {
            return null;
        }

        return $this->selectFirstStand(
            $this->applyOrderingToStandsQuery(
                $this->joinOtherStandRequests(
                    $this->sizeAppropriateAvailableStandsAtAirfield($aircraft)
                        ->airline($aircraft->airline_id)
                        ->whereIn('airline_stand.callsign_slug', $this->getCallsignSlugs($aircraft))
                        ->orderByRaw('airline_stand.callsign_slug IS NOT NULL')
                        ->orderByRaw('LENGTH(airline_stand.callsign_slug) DESC'),
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
