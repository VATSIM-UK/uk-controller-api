<?php

namespace App\Allocator\Stand;

use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AirlineAircraftArrivalStandAllocator implements ArrivalStandAllocator, RankableArrivalStandAllocator
{
    use SelectsFromAirlineSpecificStands;

    /**
     * This allocator uses the standard SelectsFromAirlineSpecificStands trait to generate a stand query,
     * with additional filters that only stands for a specific aircraft type are selected.
     */
    public function allocate(NetworkAircraft $aircraft): ?int
    {
        // We cant allocate a stand if we don't know the airline or aircraft type
        if ($aircraft->airline_id === null || $aircraft->aircraft_id === null) {
            return null;
        }

        return $this->selectAirlineSpecificStands(
            $aircraft,
            fn(Builder $query) => $query->where('airline_stand.aircraft_id', $aircraft->aircraft_id),
        );
    }

    // TODO: Move airline and aircraft checks into a separate class
    public function getRankedStandAllocation(NetworkAircraft $aircraft): Collection
    {
        // We cant allocate a stand if we don't know the airline or aircraft type
        if ($aircraft->airline_id === null || $aircraft->aircraft_id === null) {
            return collect();
        }

        return $this->selectRankedAirlineSpecificStands(
            $aircraft,
            fn(Builder $query) => $query->where('airline_stand.aircraft_id', $aircraft->aircraft_id)
        );
    }
}
