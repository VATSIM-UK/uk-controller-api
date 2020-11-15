<?php

namespace App\Allocator\Stand;

use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;

abstract class AbstractArrivalStandAllocator implements ArrivalStandAllocatorInterface
{
    public function allocate(NetworkAircraft $aircraft): ?StandAssignment
    {
        foreach ($this->getPossibleStands($aircraft) as $stand) {
            try {
                return StandAssignment::updateOrCreate(
                    [
                        'callsign' => $aircraft['callsign'],
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

    protected function getArrivalAirfieldStandQuery(NetworkAircraft $aircraft): Builder
    {
        return Stand::whereHas('airfield', function (Builder $query) use ($aircraft) {
            $query->where('code', $aircraft->planned_destairport);
        })
            ->available();
    }

    /**
     * @param NetworkAircraft $aircraft
     * @return Collection|Stand[]
     */
    protected abstract function getPossibleStands(NetworkAircraft $aircraft): Collection;
}
