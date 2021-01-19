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
        $airline = $this->airlineService->getAirlineForAircraft($aircraft);
        if ($airline === null) {
            return new Collection();
        }

        return $this->getArrivalAirfieldStandQuery($aircraft)
            ->airline($airline)
            ->orderByRaw('airline_stand.destination IS NULL DESC')
            ->inRandomOrder()
            ->get();
    }
}
