<?php

namespace App\Allocator\Stand;

use App\Models\Stand\Stand;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
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

        $stands = $this->getArrivalAirfieldStandQuery($aircraft)
            ->with('airlines')
            ->airlineDestination($airline, $this->getDestinationStrings($aircraft))
            ->get();

        return $stands->sortByDesc(function (Stand $stand) {
            return strlen((string) $stand->airlines->first()->pivot->destination);
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
