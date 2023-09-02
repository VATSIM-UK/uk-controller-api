<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;

/**
 * Secondary cargo stand allocator, with no airline preferences. Only concerned with FP remarks explicitly
 * stating that the flight is cargo - which means a cargo stand should be given.
 */
class CargoFlightArrivalStandAllocator implements ArrivalStandAllocator
{
    use SelectsStandsUsingStandardConditions;
    use ChecksForCargoAirlines;

    private AirlineService $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    /**
     * This allocator:
     * 
     * - Only allocates cargo stands to cargo airlines
     * - Orders by common conditions (see OrdersStandsByCommonConditions)
     * - Selects the first available stand (see SelectsFirstApplicableStand)
     */
    public function allocate(NetworkAircraft $aircraft): ?int
    {
        if (!$this->isCargoFlight($aircraft)) {
            return null;
        }

        return $this->selectStandsUsingStandardConditions(
            $aircraft,
            fn(Builder $query) => $query->cargo(),
            $this->commonOrderByConditions
        );
    }
}
