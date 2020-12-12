<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Collection;

class AirlineArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    private AirlineService $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    protected function getPossibleStands(NetworkAircraft $aircraft): Collection
    {
        if (($airline = $this->airlineService->getAirlineForAircraft($aircraft)) === null) {
            return new Collection();
        }

        return $this->getArrivalAirfieldStandQuery($aircraft)
            ->airline($airline)
            ->inRandomOrder()
            ->get();
    }
}
