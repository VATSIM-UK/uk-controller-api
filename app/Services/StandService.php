<?php

namespace App\Services;

use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Exceptions\Stand\StandAlreadyAssignedException;
use App\Exceptions\Stand\StandNotFoundException;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class StandService
{
    public function getStandsDependency(): Collection
    {
        return Stand::with('airfield')->get()->map(function (Stand $stand) {
            return [
                'id' => $stand->id,
                'airfield_icao' => $stand->airfield->code,
                'identifier' => $stand->identifier,
            ];
        })->toBase();
    }

    public function getStandAssignments(): Collection
    {
        return StandAssignment::all()->mapWithKeys(function (StandAssignment $assignment) {
            return [$assignment->callsign => $assignment->stand_id];
        })->toBase();
    }

    public function assignStandToAircraft(string $callsign, int $standId): void
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

    public function deleteStandAssignment(string $callsign): void
    {
        if (!StandAssignment::destroy($callsign)) {
            return;
        }

        event(new StandUnassignedEvent($callsign));
    }

    public function getDepartureStandAssignmentForAircraft(NetworkAircraft $aircraft): ?StandAssignment
    {
        return StandAssignment::where('callsign', $aircraft->callsign)
            ->whereHas('stand.airfield', function (Builder $query) use ($aircraft) {
                $query->where('code', $aircraft->planned_depairport);
            })
            ->first();
    }
}
