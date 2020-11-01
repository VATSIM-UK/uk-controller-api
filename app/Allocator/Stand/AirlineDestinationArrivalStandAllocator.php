<?php

namespace App\Allocator\Stand;

use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

class AirlineDestinationArrivalStandAllocator extends AbstractArrivalStandAllocator
{
    /**
     * @var AirlineService
     */
    private $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    public function getPossibleStands(NetworkAircraft $aircraft): Collection
    {
        return Stand::whereHas('airfield', function (Builder $query) use ($aircraft) {
            $query->where('code', $aircraft->planned_arrairport);
        })
            ->whereIn()
            ->unassigned()
            ->unoccupied()
            ->get();
    }

    public function getDestinationStrings(NetworkAircraft $aircraft): array
    {
        return [
            substr($aircraft->planned_depairport, 0, 1),
            substr($aircraft->planned_depairport, 0, 2),
            substr($aircraft->planned_depairport, 0, 3),
            $aircraft->planned_depairport
        ];
    }
}
