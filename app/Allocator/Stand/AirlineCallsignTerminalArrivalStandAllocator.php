<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AirlineCallsignTerminalArrivalStandAllocator implements ArrivalStandAllocator, RankableArrivalStandAllocator
{
    use UsesCallsignSlugs;
    use SelectsStandsFromAirlineSpecificTerminals;

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
     * a specific callsign
     * - Orders these stands by the airline's priority for the stand
     * - Orders these stands by the common conditions, minus the general allocation priority
     * (see OrdersStandsByCommonConditions)`
     * - Selects the first stand that pops up
     */
    public function allocate(NetworkAircraft $aircraft): ?int
    {
        // If the aircraft doesnt have an airline, we cant allocate a stand
        if ($aircraft->airline_id === null || $aircraft->aircraft_id === null) {
            return null;
        }

        return $this->selectStandsAtAirlineSpecificTerminals(
            $aircraft,
            $this->queryFilter($aircraft)
        );
    }

    public function getRankedStandAllocation(NetworkAircraft $aircraft): Collection
    {
        // If the aircraft doesnt have an airline, we cant allocate a stand
        if ($aircraft->airline_id === null || $aircraft->aircraft_id === null) {
            return collect();
        }

        return $this->selectRankedStandsAtAirlineSpecificTerminals(
            $aircraft,
            $this->queryFilter($aircraft)
        );
    }

    private function queryFilter(NetworkAircraft $aircraft): Closure
    {
        return fn (Builder $query)
            => $query->where('airline_terminal.full_callsign', $this->getFullCallsignSlug($aircraft));
    }
}
