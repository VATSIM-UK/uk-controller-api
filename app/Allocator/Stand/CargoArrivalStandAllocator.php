<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CargoArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    private AirlineService $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    protected function getOrderedStandsQuery(Builder $stands, NetworkAircraft $aircraft): ?Builder
    {
        if (
            (($airline = $this->airlineService->getAirlineForAircraft($aircraft)) === null ||
                !$airline->is_cargo) &&
            !$this->hasCargoRemarks($aircraft)
        ) {
            return null;
        }

        return $stands->cargo();
    }

    private function hasCargoRemarks(NetworkAircraft $aircraft): bool
    {
        return isset($aircraft->remarks) &&
            Str::contains(Str::upper($aircraft->remarks), 'RMK/CARGO');
    }
}
