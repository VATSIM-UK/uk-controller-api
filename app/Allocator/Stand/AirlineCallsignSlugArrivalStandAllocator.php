<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;

class AirlineCallsignSlugArrivalStandAllocator implements ArrivalStandAllocator
{
    use SelectsFromAirlineSpecificStands;
    use UsesCallsignSlugs;

    private const ORDER_BYS = [
        'airline_stand.callsign_slug IS NOT NULL',
        'LENGTH(airline_stand.callsign_slug) DESC',
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

        return $this->selectAirlineSpecificStands(
            $aircraft,
            fn(Builder $query) => $query->whereIn('airline_stand.callsign_slug', $this->getCallsignSlugs($aircraft)),
            self::ORDER_BYS
        );
    }
}
