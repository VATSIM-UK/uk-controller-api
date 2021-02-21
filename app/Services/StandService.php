<?php

namespace App\Services;

use App\Allocator\Stand\ArrivalStandAllocatorInterface;
use App\Events\StandAssignedEvent;
use App\Events\StandOccupiedEvent;
use App\Events\StandUnassignedEvent;
use App\Events\StandVacatedEvent;
use App\Exceptions\Stand\StandAlreadyAssignedException;
use App\Exceptions\Stand\StandNotFoundException;
use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Airfield;
use App\Models\Airline\Airline;
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
    private const MAX_OCCUPANCY_SPEED = 0;

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
    private const MAX_OCCUPANCY_DISTANCE_METERS = 25;

    /**
     * How many minutes before arrival the stand should be assigned
     */
    private const ASSIGN_STAND_MINUTES_BEFORE = 15.0;

    private $allStands = [];

    /**
     * @var ArrivalStandAllocatorInterface[]
     */
    private $allocators;

    /**
     * @param ArrivalStandAllocatorInterface[] $allocators
     */
    public function __construct(array $allocators)
    {
        $this->allocators = $allocators;
    }

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

    public function getAvailableStandsForAirfield(string $airfield): Collection
    {
        return Stand::available()->airfield($airfield)->get()->map(function (Stand $stand) {
            return $stand->identifier;
        })->toBase();
    }

    public function getAssignedStandForAircraft(string $aircraft): ?Stand
    {
        $assignment = StandAssignment::with('stand', 'stand.airfield')->find($aircraft);
        return $assignment ? $assignment->stand : null;
    }

    /**
     * Assignments are preferred to reservations as reservations may be overridden by controllers.
     *
     * Get all assigned stands as well as any active reservations.
     */
    public function getAirfieldStandStatus(string $airfield): array
    {
        $stands = Stand::with(
            'wakeCategory',
            'maxAircraft',
            'assignment',
            'occupier',
            'activeReservations',
            'pairedStands.assignment',
            'pairedStands.occupier',
            'pairedStands.activeReservations'
        )
            ->airfield($airfield)
            ->get();
        $stands->sortBy('identifier', SORT_NATURAL);

        $standStatuses = [];

        foreach ($stands as $stand) {
            $standStatuses[] = $this->getStandStatus($stand);
        }

        return $standStatuses;
    }

    private function getStandStatus(Stand $stand): array
    {
        $standData = [
            'identifier' => $stand->identifier,
            'type' => $stand->type ? $stand->type->key : null,
            'airlines' => $stand->airlines->groupBy('icao_code')->map(function (Collection $airlineDestination) {
                return $airlineDestination->filter(function (Airline $airline) {
                    return $airline->pivot->destination;
                })->map(function (Airline $airline) {
                    return $airline->pivot->destination;
                });
            })->toArray(),
            'max_wake_category' => $stand->wakeCategory ? $stand->wakeCategory->code: null,
            'max_aircraft_type' => $stand->maxAircraft ? $stand->maxAircraft->code : null,
        ];
        if ($stand->occupier->first()) {
            $standData['status'] = 'occupied';
            $standData['callsign'] = $stand->occupier->first()->callsign;
        } elseif ($stand->assignment) {
            $standData['status'] = 'assigned';
            $standData['callsign'] = $stand->assignment->callsign;
        } elseif (!$stand->activeReservations->isEmpty()) {
            $standData['status'] = 'reserved';
            $standData['callsign'] = $stand->activeReservations->first()->callsign;
        } elseif (!$stand->reservationsInNextHour->isEmpty()) {
            $standData['status'] = 'reserved_soon';
            $standData['reserved_at'] = $stand->reservationsInNextHour->first()->start;
            $standData['callsign'] = $stand->reservationsInNextHour->first()->callsign;
        } elseif (
            !$stand->pairedStands->filter(function (Stand $stand) {
                return $stand->assignment ||
                    !$stand->occupier->isEmpty() ||
                    !$stand->activeReservations->isEmpty();
            })->isEmpty()
        ) {
            $standData['status'] = 'unavailable';
        } else {
            $standData['status'] = 'available';
        }

        return $standData;
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

        $this->createStandAssignment($callsign, $standId);
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
        $currentAssignment = StandAssignment::with('aircraft', 'stand.pairedStands.assignment')
            ->where('stand_id', $standId)
            ->first();

        // Remove the current assignment
        if ($currentAssignment && $currentAssignment->callsign !== $callsign) {
            $this->deleteStandAssignmentByCallsign($currentAssignment->aircraft->callsign);
        }

        // Remove assignments on paired stands
        $stand = Stand::with('pairedStands.assignment')->find($standId);
        foreach ($stand->pairedStands as $pairedStand) {
            if ($pairedStand->assignment && $pairedStand->assignment->callsign !== $callsign) {
                $this->deleteStandAssignmentByCallsign($pairedStand->assignment->callsign);
            }
        }

        $this->createStandAssignment($callsign, $standId);
    }

    private function createStandAssignment(string $callsign, int $standId): void
    {
        $assignment = StandAssignment::updateOrCreate(
            ['callsign' => $callsign],
            [
                'stand_id' => $standId,
            ]
        );

        event(new StandAssignedEvent($assignment));
    }

    public function deleteStandAssignmentByCallsign(string $callsign): void
    {
        if (!StandAssignment::destroy($callsign)) {
            return;
        }

        event(new StandUnassignedEvent($callsign));
    }

    public function deleteStandAssignment(StandAssignment $assignment): void
    {
        $this->deleteStandAssignmentByCallsign($assignment->callsign);
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

    private function vacateStand(NetworkAircraft $aircraft)
    {
        $aircraft->occupiedStand()->sync([]);
        event(new StandVacatedEvent($aircraft));
    }

    public function setOccupiedStand(NetworkAircraft $aircraft): ?Stand
    {
        // If an aircraft cannot occupy a stand, we don't need to check any further. Vacate stand if there is one.
        if (!$this->canOccupyStand($aircraft)) {
            if ($aircraft->occupiedStand()->exists()) {
                $this->vacateStand($aircraft);
            }
            return null;
        }

        /*
         * The aircraft is still occupying its stand.
         */
        if ($aircraft->occupiedStand->first() && $this->standOccupied($aircraft, $aircraft->occupiedStand()->first())) {
            return $aircraft->occupiedStand->first();
        }

        // If there's a stand that's viable, occupy the stand.
        if ($selectedStand = $this->getOccupiedStand($aircraft)) {
            $this->occupyStand($aircraft, $selectedStand);
        }

        return $selectedStand;
    }

    /**
     * Get the stand that the aircraft is closest to, within occupation parameters.
     */
    private function getOccupiedStand(NetworkAircraft $aircraft): ?Stand
    {
        $selectedStand = null;
        $selectedStandDistance = PHP_INT_MAX;

        foreach ($this->getAllStands() as $stand) {
            $distanceFromStand = $stand->coordinate->getDistance($aircraft->latLong, new Haversine());

            if (
                $this->standOccupied($aircraft, $stand) &&
                $distanceFromStand < $selectedStandDistance
            ) {
                $selectedStand = $stand;
                $selectedStandDistance = $distanceFromStand;
            }
        }

        return $selectedStand;
    }

    /*
     * Delete any stand assignment that isn't for the given aircraft
     */
    private function occupyStand(NetworkAircraft $aircraft, Stand $stand)
    {
        // Remove any conflicting assignments
        $conflictingAssignment = StandAssignment::where('callsign', '<>', $aircraft->callsign)
            ->where('stand_id', $stand->id)
            ->first();

        if ($conflictingAssignment) {
            $this->deleteStandAssignment($conflictingAssignment);
        }

        $alreadyOccupiedStand = $aircraft->occupiedStand->first();
        $alreadyOccupied = $alreadyOccupiedStand !== null &&
            $alreadyOccupiedStand->id === $stand->id;

        // Mark the stand as occupied
        $aircraft->occupiedStand()->sync([$stand->id]);

        // Trigger an event so other listeners can process it if its a new occupation
        if (!$alreadyOccupied) {
            event(new StandOccupiedEvent($aircraft, $stand));
        }
    }

    private function standOccupied(NetworkAircraft $aircraft, Stand $stand): bool
    {
        $distanceFromStand = $stand->coordinate->getDistance($aircraft->latLong, new Haversine());
        return $distanceFromStand < self::MAX_OCCUPANCY_DISTANCE_METERS &&
            $this->canOccupyStand($aircraft);
    }

    private function canOccupyStand(NetworkAircraft $aircraft)
    {
        return $aircraft->altitude <= self::MAX_OCCUPANCY_ALTITUDE &&
            $aircraft->groundspeed <= self::MAX_OCCUPANCY_SPEED;
    }

    /**
     * Use the stand assignment rules to allocate a stand for a given aircraft
     */
    public function allocateStandForAircraft(NetworkAircraft $aircraft): ?StandAssignment
    {
        if (!$this->shouldAllocateStand($aircraft)) {
            return null;
        }

        foreach ($this->allocators as $allocator) {
            if ($allocation = $allocator->allocate($aircraft)) {
                event(new StandAssignedEvent($allocation));
                return $allocation;
            }
        }

        return null;
    }

    public function removeAllocationIfDestinationChanged(NetworkAircraft $aircraft): void
    {
        if (
            ($assignedStand = $this->getAssignedStandForAircraft($aircraft->callsign)) !== null &&
            $assignedStand->airfield->code !== $aircraft->planned_destairport
        ) {
            $this->deleteStandAssignmentByCallsign($aircraft->callsign);
        }
    }

    public function getAircraftEligibleForArrivalStandAllocation(): Collection
    {
        return NetworkAircraft::whereIn(
            'planned_destairport',
            Airfield::all()->pluck('code')->toArray()
        )->get();
    }

    /**
     * Criteria for whether a stand should be allocated
     *
     * 1. Cannot have the same departure and arrival airport (to cater for circuits)
     * 2. Aircraft must not have an existing stand assignment
     * 3. The arrival airfield must exist
     * 4. The aircraft has to be moving (to prevent divide by zero errors)
     * 5. The aircraft must have a discernible aircraft type
     * 6. The aircraft type should be one that we allocate stands to
     * 7. The aircraft needs to be within a certain number of minutes from landing
     */
    private function shouldAllocateStand(NetworkAircraft $aircraft): bool
    {
        return $aircraft->planned_depairport !== $aircraft->planned_destairport &&
            StandAssignment::where('callsign', $aircraft->callsign)->doesntExist() &&
            ($arrivalAirfield = Airfield::where('code', $aircraft->planned_destairport)->first()) !== null &&
            $aircraft->groundspeed &&
            ($aircraftType = Aircraft::where('code', $aircraft->aircraftType)->first()) &&
            $aircraftType->allocate_stands &&
            $this->getTimeFromAirfieldInMinutes($aircraft, $arrivalAirfield) < self::ASSIGN_STAND_MINUTES_BEFORE;
    }

    /**
     * Ground speed is kts (nautical miles per hour), so for minutes multiply that by 60.
     *
     * @param NetworkAircraft $aircraft
     * @param Airfield $airfield
     * @return float
     */
    private function getTimeFromAirfieldInMinutes(NetworkAircraft $aircraft, Airfield $airfield): float
    {
        $distanceToAirfieldInNm = LocationService::metersToNauticalMiles(
            $aircraft->latLong->getDistance($airfield->coordinate, new Haversine())
        );
        $groundspeed = $aircraft->groundspeed === 0 ? 1 : $aircraft->groundspeed;

        return (float) ($distanceToAirfieldInNm / $groundspeed) * 60.0;
    }

    /**
     * @return string[]
     */
    public function getAllocatorPreference(): array
    {
        return array_map(
            function (ArrivalStandAllocatorInterface $allocator) {
                return get_class($allocator);
            },
            $this->allocators
        );
    }
}
