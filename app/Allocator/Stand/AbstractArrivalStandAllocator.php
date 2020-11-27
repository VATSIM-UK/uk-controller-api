<?php

namespace App\Allocator\Stand;

use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
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
                if ($queryException->errorInfo[1] !== 1062) {
                    throw $queryException;
                }
            }
        }

        return null;
    }

    /*
     * Return base query for stands at the arrival airfield, which are of a suitable
     * size (or max size if no type) for the aircraft and not occupied.
     */
    final protected function getArrivalAirfieldStandQuery(NetworkAircraft $aircraft): Builder
    {
        $aircraftType = Aircraft::where('code', $aircraft->aircraftType)->first();

        return Stand::whereHas('airfield', function (Builder $query) use ($aircraft) {
            $query->where('code', $aircraft->planned_destairport);
        })
            ->sizeAppropriate($aircraftType)
            ->available()
            ->orderByWeight()
            ->select('stands.*');
    }

    /**
     * @param NetworkAircraft $aircraft
     * @return Collection|Stand[]
     */
    abstract protected function getPossibleStands(NetworkAircraft $aircraft): Collection;
}
