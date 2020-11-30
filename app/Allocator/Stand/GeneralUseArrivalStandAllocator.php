<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Collection;

class GeneralUseArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    /**
     * @var AirlineService
     */
    private $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    /**
     * This runs the base query, and gets stands at the arrival airport suitable
     * for the aircraft's size that aren't occupied.
     *
     * @param NetworkAircraft $aircraft
     * @return Collection
     */
    protected function getPossibleStands(NetworkAircraft $aircraft): Collection
    {
        return $this->getArrivalAirfieldStandQuery($aircraft)
            ->generalUse()
            ->notCargo()
            ->get();
    }
}
