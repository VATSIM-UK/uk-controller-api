<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;

/**
 * Secondary cargo stand allocator, with no airline preferences. Only concerned with FP remarks explicitly
 * stating that the flight is cargo - which means a cargo stand should be given.
 */
class CargoFlightArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    use ChecksForCargoAirlines;

    private AirlineService $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    protected function getOrderedStandsQuery(Builder $stands, NetworkAircraft $aircraft): ?Builder
    {
        if (!$this->isCargoFlight($aircraft)) {
            return null;
        }

        return $stands->cargo();
    }
}
