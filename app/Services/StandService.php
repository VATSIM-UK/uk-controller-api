<?php

namespace App\Services;

use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use Illuminate\Support\Collection;

class StandService
{
    public function getStandsDependency(): Collection
    {
        return Stand::all()->groupBy('airfield_id')->mapWithKeys(function (Collection $collection) {
            return [
                Airfield::find($collection->first()->airfield_id)->code => $collection->map(function (Stand $stand) {
                    return [
                        'id' => $stand->id,
                        'identifier' => $stand->identifier
                    ];
                }),
            ];
        })->toBase();
    }

    public function getStandAssignments(): Collection
    {
        return StandAssignment::all()->mapWithKeys(function (StandAssignment $assignment) {
            return [$assignment->callsign => $assignment->stand_id];
        })->toBase();
    }
}
