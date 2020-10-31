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
use Location\Distance\Haversine;

class StandService
{
    public const STAND_DEPENDENCY_KEY = 'DEPENDENCY_STANDS';

    // The maximum speed at which an aircraft can be travelling for it to be deemed to be occupying a stand
    private const MAX_OCCUPANCY_SPEED = 25;

    /*
     * The maximum altitude that an aircraft may be at in order to be deemed to be occupying a stand. For reference,
     * the highest airport in the UK is Leeds at 681ft AMSL.
     */
    private const MAX_OCCUPANCY_ALTITUDE = 750;

    /*
     * Max distance that the aircraft must be from the centre point of the stand to be considered
     * to be occupying it.
     *
     * As a reference, a small stand at Heathrow is about 50m wide and 60m deep.
     */
    private const MAX_OCCUPANCY_DISTANCE_KILOMETERS = 0.025;

    private $allStands = [];

    public function getStandsDependency(): Collection
    {
        return $this->getAllStands()->groupBy('airfield_id')->mapWithKeys(
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
        if (($stand = $this->getStandByAirfieldAndIdentifer($airfield, $identifier)) === null) {
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
    public function changeStandIdentifier(string $airfield, string $oldIdentifier, string $newIdentifier): void
    {
        if (($stand = $this->getStandByAirfieldAndIdentifer($airfield, $oldIdentifier)) === null) {
            return;
        }

        $stand->identifier = $newIdentifier;
        $stand->save();
        DependencyService::touchDependencyByKey(self::STAND_DEPENDENCY_KEY);
    }

    /**
     * Returns a stand by airfield ICAO and identifier
     *
     * @param string $airfield
     * @param string $identifier
     * @return Stand|null
     */
    private function getStandByAirfieldAndIdentifer(string $airfield, string $identifier): ?Stand
    {
        return Stand::with('airfield')->whereHas(
            'airfield',
            function (Builder $query) use ($airfield) {
                $query->where('code', $airfield);
            }
        )->where('identifier', $identifier)
            ->first();
    }

    /**
     * @return Collection|Stand[]
     */
    private function getAllStands(): Collection
    {
        $this->allStands = Stand::all()->toBase();
        return $this->allStands;
    }

    public function getOccupiedStand(NetworkAircraft $aircraft): ?Stand
    {
        if ($aircraft->altitude > self::MAX_OCCUPANCY_ALTITUDE || $aircraft->groundspeed > self::MAX_OCCUPANCY_SPEED) {
            return null;
        }

        $selectedStand = null;
        $selectedStandDistance = PHP_INT_MAX;

        foreach ($this->getAllStands() as $stand)
        {
            $distanceFromStand = $stand->coordinate->getDistance($aircraft->latLong, new Haversine());
            if (
                $distanceFromStand < self::MAX_OCCUPANCY_DISTANCE_KILOMETERS &&
                $distanceFromStand < $selectedStandDistance
            ) {
                $selectedStand = $stand;
                $selectedStandDistance = $distanceFromStand;
            }
        }

        return $selectedStand;
    }
}
