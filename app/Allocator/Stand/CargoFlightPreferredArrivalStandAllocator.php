<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * The primary arrival stand allocator for cargo. Looks for either a cargo airline
 * OR remarks that indicate cargo in the flightplan. If this is satisfied, it will look
 * for any cargo stand the airline uses and allocate that.
 *
 * This allows airlines that also handle passengers to have stands for their cargo operation.
 */
class CargoFlightPreferredArrivalStandAllocator implements ArrivalStandAllocator
{
    use SelectsFromAirlineSpecificStands;
    use ChecksForCargoAirlines;

    private AirlineService $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    public function allocate(NetworkAircraft $aircraft): ?int
    {
        // If the aircraft isn't a cargo airline or a cargo flight, this rule doesn't apply
        if (!$this->isCargoAirline($aircraft) && !$this->isCargoFlight($aircraft)) {
            return null;
        }

        // If the aircarft has no airline at all, there's nothing we can do
        if ($aircraft->airline_id === null) {
            return null;
        }

        return $this->selectAirlineSpecificStands(
            $aircraft,
            $this->queryFilter()
        );
    }

    public function getRankedStandAllocation(NetworkAircraft $aircraft): Collection
    {
        // If the aircraft doesnt have an airline, we cant allocate a stand
        if (!$this->isCargoAirline($aircraft) && !$this->isCargoFlight($aircraft)) {
            return collect();
        }
        
        return $this->selectRankedAirlineSpecificStands(
            $aircraft,
            $this->queryFilter()
        );
    }

    private function queryFilter(): Closure
    {
        return fn (Builder $query) => $query->cargo();
    }
}
