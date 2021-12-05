<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CargoAirlineArrivalStandAllocator extends AbstractArrivalStandAllocator
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

        if (!($airline = $this->airlineService->getAirlineForAircraft($aircraft))) {
            return null;
        }

        return $stands->cargo()
            ->airline($airline)
            ->orderBy('airline_stand.priority');
    }
}
