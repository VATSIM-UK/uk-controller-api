<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\AirlineService;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * A fallback allocator for cargo airlines. Will allocate any
 * cargo stand to any airline that is type cargo.
 */
class CargoAirlineFallbackStandAllocator implements ArrivalStandAllocator, RankableArrivalStandAllocator
{
    use ChecksForCargoAirlines;
    use SelectsStandsUsingStandardConditions;

    private AirlineService $airlineService;

    public function __construct(AirlineService $airlineService)
    {
        $this->airlineService = $airlineService;
    }

    /**
     * This allocator:
     *
     * - Only allocates cargo stands to cargo airlines
     * - Orders by common conditions (see OrdersStandsByCommonConditions)
     * - Selects the first available stand (see SelectsFirstApplicableStand)
     */
    public function allocate(NetworkAircraft $aircraft): ?int
    {
        if ($aircraft->aircraft_id === null || !$this->isCargoAirline($aircraft)) {
            return null;
        }

        return $this->selectStandsUsingStandardConditions(
            $aircraft,
            $this->queryFilter()
        );
    }

    public function getRankedStandAllocation(NetworkAircraft $aircraft): Collection
    {
        if ($aircraft->aircraft_id === null || !$this->isCargoAirline($aircraft)) {
            return collect();
        }

        return $this->selectRankedStandsUsingStandardConditions(
            $aircraft,
            $this->queryFilter()
        );
    }

    private function queryFilter(): Closure
    {
        return fn (Builder $query) => $query->cargo();
    }
}
