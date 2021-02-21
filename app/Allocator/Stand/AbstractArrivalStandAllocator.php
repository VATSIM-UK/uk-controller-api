<?php

namespace App\Allocator\Stand;

use App\Models\Aircraft\Aircraft;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;

abstract class AbstractArrivalStandAllocator implements ArrivalStandAllocatorInterface
{
    final public function allocate(NetworkAircraft $aircraft): ?StandAssignment
    {
        foreach ($this->getPossibleStands($aircraft) as $stand) {
            try {
                return StandAssignment::updateOrCreate(
                    [
                        'callsign' => $aircraft['callsign']
                    ],
                    [
                        'stand_id' => $stand->id,
                    ]
                );
            } catch (QueryException $queryException) {
                // If it's a duplicate stand, ignore it and try a different stand
                if ($queryException->errorInfo[1] !== 1062) {
                    throw $queryException;
                }
            }
        }

        return null;
    }

    /*
     * Base query for stands at the arrival airfield, which are of a suitable
     * size (or max size if no type) for the aircraft and not occupied.
     */
    private function getArrivalAirfieldStandQuery(NetworkAircraft $aircraft): Builder
    {
        $aircraftType = Aircraft::where('code', $aircraft->aircraftType)->first();

        return Stand::whereHas('airfield', function (Builder $query) use ($aircraft) {
            $query->where('code', $aircraft->planned_destairport);
        })
            ->sizeAppropriate($aircraftType)
            ->available()
            ->select('stands.*');
    }

    /**
     * Get all the possible stands that are available for allocation.
     *
     * @param NetworkAircraft $aircraft
     * @return Collection|Stand[]
     */
    private function getPossibleStands(NetworkAircraft $aircraft): Collection
    {
        $orderedQuery = $this->getOrderedStandsQuery($this->getArrivalAirfieldStandQuery($aircraft), $aircraft);
        return $orderedQuery === null
            ? new Collection()
            : $this->applyBaseOrderingToStandsQuery($orderedQuery)->get();
    }

    /**
     * Apply the base ordering to the stands query. This orders stands by weight ascending
     * so smaller aircraft prefer smaller stands and also applies an element of randomness
     * so we don't just put all the aircraft next to each other.
     *
     * @param Builder $query
     * @return Builder
     */
    private function applyBaseOrderingToStandsQuery(Builder $query): Builder
    {
        return $query->orderByWeight()->inRandomOrder();
    }

    abstract protected function getOrderedStandsQuery(Builder $stands, NetworkAircraft $aircraft): ?Builder;
}
