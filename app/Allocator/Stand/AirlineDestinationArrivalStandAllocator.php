<?php

namespace App\Allocator\Stand;

use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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

    protected function getPossibleStands(NetworkAircraft $aircraft): Collection
    {
        if (($airline = $this->airlineService->getAirlineForAircraft($aircraft)) === null)
        {
            return new Collection();
        }

        $stands = Stand::whereHas('airfield', function (Builder $query) use ($aircraft) {
            $query->where('code', $aircraft->planned_destairport);
        })
            ->airlineDestination($airline, $this->getDestinationStrings($aircraft))
            ->available()
            ->get();

        dd($stands[0]);

        dd($stands->sortByDesc(function (Stand $stand) {
            dd($stand);
            return strlen((string) $stand->destination);
        }));

        return $stands->sortByDesc(function (Stand $stand) {
            return strlen((string) $stand->destination);
        });
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
