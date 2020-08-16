<?php

namespace App\Services;

use App\Events\StandAssignedEvent;
use App\Exceptions\Stand\StandAlreadyAssignedException;
use App\Exceptions\Stand\StandNotFoundException;
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

    public function assignStandToAircraft(string $callsign, int $standId)
    {
        $stand = Stand::find($standId);
        if (!$stand) {
            throw new StandNotFoundException(sprintf('Stand with id %d not found', $standId));
        }

        NetworkDataService::firstOrCreateNetworkAircraft($callsign);
        $currentAssignment = StandAssignment::where('stand_id', $standId)->first();

        if ($currentAssignment && $currentAssignment->callsign !== $callsign) {
            throw new StandAlreadyAssignedException(
                sprintf('Stand id %d is already assigned to %s', $standId, $currentAssignment->callsign)
            );
        }

        $assignment = StandAssignment::updateOrCreate(
            ['callsign' => $callsign],
            [
                'callsign' => $callsign,
                'stand_id' => $standId,
            ]
        );

        event(new StandAssignedEvent($assignment));
    }
}
