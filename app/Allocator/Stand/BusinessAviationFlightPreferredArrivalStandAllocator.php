<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * The primary arrival stand allocator for business aviation. Looks for a business aviation
 * aircraft in the flightplan. If this is satisfied, it will look
 * for any business aviation stand the airline uses and allocate that.
 */
class BusinessAviationFlightPreferredArrivalStandAllocator implements ArrivalStandAllocator
{
    use SelectsFromAirlineSpecificStands;
    use ChecksForBusinessAviationAircraft;

    private AirlineService $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    public function allocate(NetworkAircraft $aircraft): ?int
    {
        // If the aircraft isn't a business aviation aircraft, this rule doesn't apply
        if (!$this->isBusinessAviationAircraft($aircraft)) {
            return null;
        }

        return $this->selectAirlineSpecificStands(
            $aircraft,
            $this->queryFilter()
        );
    }

    public function getRankedStandAllocation(NetworkAircraft $aircraft): Collection
    {
        // If the aircraft isn't a business aviation aircraft, we cant allocate a stand
        if (!$this->isBusinessAviationAircraft($aircraft)) {
            return collect();
        }
        
        return $this->selectRankedAirlineSpecificStands(
            $aircraft,
            $this->queryFilter()
        );
    }

    private function queryFilter(): Closure
    {
        return fn (Builder $query) => $query->businessAviation();
    }
}
