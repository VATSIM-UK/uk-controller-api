<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;

class CargoArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    use ChecksForCargoAirlines;

    private AirlineService $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    protected function getOrderedStandsQuery(Builder $stands, NetworkAircraft $aircraft): ?Builder
    {
        if (!$this->isCargoAirline($aircraft, $this->airlineService)) {
            return null;
        }

        return $stands->cargo();
    }
}
