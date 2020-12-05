<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Collection;

class CargoArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    /**
     * @var AirlineService
     */
    private $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    protected function getPossibleStands(NetworkAircraft $aircraft): Collection
    {
        if (
            ($airline = $this->airlineService->getAirlineForAircraft($aircraft)) === null ||
            !$airline->is_cargo
        ) {
            return new Collection();
        }

        return $this->getArrivalAirfieldStandQuery($aircraft)
            ->cargo()
            ->inRandomOrder()
            ->get();
    }
}
