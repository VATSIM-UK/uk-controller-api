<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;

/**
 * A fallback allocator for cargo airlines. Will allocate any
 * cargo stand to any airline that is type cargo.
 */
class CargoAirlineFallbackStandAllocator extends AbstractArrivalStandAllocator
{
    use ChecksForCargoAirlines;

    private AirlineService $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    protected function getOrderedStandsQuery(Builder $stands, NetworkAircraft $aircraft): ?Builder
    {
        if (!$this->isCargoAirline($aircraft)) {
            return null;
        }

        return $stands->cargo();
    }
}
