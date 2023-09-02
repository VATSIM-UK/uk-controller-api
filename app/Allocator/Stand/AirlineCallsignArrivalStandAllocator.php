<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;

class AirlineCallsignArrivalStandAllocator implements ArrivalStandAllocator
{
    use SelectsFromAirlineSpecificStands;
    use UsesCallsignSlugs;

    private readonly AirlineService $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    /**
     * This allocator:
     * 
     * - Selects stands that are size appropriate and available
     * - Filters these to stands that are specifically selected for the airline and a specific callsign
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

        return $this->selectAirlineSpecificStands(
            $aircraft,
            fn(Builder $query) => $query->where('airline_stand.full_callsign', $this->getFullCallsignSlug($aircraft)),
        );
    }
}
