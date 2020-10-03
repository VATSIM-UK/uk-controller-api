<?php

namespace App\Services;

use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Exceptions\Stand\StandAlreadyAssignedException;
use App\Exceptions\Stand\StandNotFoundException;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class StandService
{
    public const STAND_DEPENDENCY_KEY = 'DEPENDENCY_STANDS';

    public function getStandsDependency(): Collection
    {
        return Stand::all()->groupBy('airfield_id')->mapWithKeys(
            function (Collection $collection) {
                return [
                    Airfield::find($collection->first()->airfield_id)->code => $collection->map(
                        function (Stand $stand) {
                            return [
                                'id' => $stand->id,
                                'identifier' => $stand->identifier
                            ];
                        }
                    ),
                ];
            }
        )->toBase();
    }

    public function getStandAssignments(): Collection
    {
        return StandAssignment::all()->map(
            function (StandAssignment $assignment) {
                return [
                    'callsign' => $assignment->callsign,
                    'stand_id' => $assignment->stand_id,
                ];
            }
        )->toBase();
    }

    /**
     * Creates a stand assignment by assigning an aircraft to a stand.
     * If the stand is already occupied, then the previous assignment is not
     * overridden.
     *
     * @param string $callsign
     * @param int $standId
     * @throws StandAlreadyAssignedException
     */
    public function assignAircraftToStand(string $callsign, int $standId): void
    {
        if (!$this->standExists($standId)) {
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

    /**
     * Creates a stand assignment by assigning a particular stand to an aircraft.
     * If the stand is already assigned, that assignment is first removed.
     *
     * @param string $callsign
     * @param int $standId
     */
    public function assignStandToAircraft(string $callsign, int $standId): void
    {
        if (!$this->standExists($standId)) {
            throw new StandNotFoundException(sprintf('Stand with id %d not found', $standId));
        }

        NetworkDataService::firstOrCreateNetworkAircraft($callsign);
        $currentAssignment = StandAssignment::with('aircraft')
            ->where('stand_id', $standId)
            ->first();

        if ($currentAssignment && $currentAssignment->callsign !== $callsign) {
            $this->deleteStandAssignment($currentAssignment->aircraft->callsign);
        }

        $assignment = StandAssignment::updateOrCreate(
            ['callsign' => $callsign],
            [
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
            ->whereHas(
                'stand.airfield',
                function (Builder $query) use ($aircraft) {
                    $query->where('code', $aircraft->planned_depairport);
                }
            )
            ->first();
    }

    private function standExists(int $standId): bool
    {
        return Stand::where('id', $standId)->exists();
    }

    /**
     * Delete a given stand
     *
     * @param string $airfield
     * @param string $identifier
     * @throws Exception
     */
    public function deleteStand(string $airfield, string $identifier): void
    {
        $stand = Stand::with('airfield')->whereHas(
            'airfield',
            function (Builder $query) use ($airfield) {
                $query->where('code', $airfield);
            }
        )->where('identifier', $identifier)
            ->first();

        if (!$stand) {
            return;
        }

        $stand->delete();
        DependencyService::touchDependencyByKey(self::STAND_DEPENDENCY_KEY);
    }

    /**
     * Change the identifier for a stand
     *
     * @param string $airfield
     * @param string $oldIdentifier
     * @param string $newIdentifier
     */
    public function changeStandIdentifier(string $airfield, string $oldIdentifier, string $newIdentifier)
    {
        $stand = Stand::with('airfield')->whereHas(
            'airfield',
            function (Builder $query) use ($airfield) {
                $query->where('code', $airfield);
            }
        )->where('identifier', $oldIdentifier)
            ->first();

        if (!$stand) {
            return;
        }

        $stand->identifier = $newIdentifier;
        $stand->save();
        DependencyService::touchDependencyByKey(self::STAND_DEPENDENCY_KEY);
    }
}
