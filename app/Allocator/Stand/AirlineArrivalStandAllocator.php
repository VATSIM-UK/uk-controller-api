<?php

namespace App\Allocator\Stand;

use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AirlineArrivalStandAllocator extends AbstractArrivalStandAllocator
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
        if (($airline = $this->airlineService->getAirlineForAircraft($aircraft)) === null)
        {
            return new Collection();
        }

        return Stand::whereHas('airfield', function (Builder $query) use ($aircraft) {
            $query->where('code', $aircraft->planned_destairport);
        })
            ->airline($airline)
            ->available()
            ->get();
    }
}
