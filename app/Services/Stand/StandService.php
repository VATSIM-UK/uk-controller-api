<?php

namespace App\Services\Stand;

use App\Allocator\Stand\ArrivalStandAllocatorInterface;
use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Exceptions\Stand\StandAlreadyAssignedException;
use App\Exceptions\Stand\StandNotFoundException;
use App\Models\Aircraft\Aircraft;
use App\Models\Airfield\Airfield;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\DependencyService;
use App\Services\LocationService;
use App\Services\NetworkAircraftService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
    private const MAX_OCCUPANCY_DISTANCE_METERS = 35;

    /**
     * How many minutes before arrival the stand should be assigned
     */
    private const ASSIGN_STAND_MINUTES_BEFORE = 15.0;

    /**
     * The maximum distance in meters from an airfields centre that we bother
     * checking if an aircraft is sat on a stand. This is about 2.5 nautical miles.
     */
    private const DISTANCE_FROM_AIRFIELD_TO_CHECK_STANDS = 5000;

    private ?Collection $allStandsByAirfield = null;

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
        return $this->getAllStandsByAirfield()
            ->mapWithKeys(
                function (Airfield $airfield) {
                    return [
                        $airfield->code => $airfield->stands
                            ->reject(fn (Stand $stand) => $stand->closed_at !== null)
                            ->values()
                            ->map(fn (Stand $stand) => [
                                'id' => $stand->id,
                                'identifier' => $stand->identifier,
                            ]),
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

        if ($stand->isClosed()) {
            $standData['status'] = 'closed';
        } elseif ($stand->occupier->first()) {
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

        NetworkAircraftService::createPlaceholderAircraft($callsign);
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

        NetworkAircraftService::createPlaceholderAircraft($callsign);
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
    private function getAllStandsByAirfield(): Collection
    {
        if (!$this->allStandsByAirfield) {
            $this->allStandsByAirfield = Airfield::with('stands')->whereHas('stands')->get()->toBase();
        }

        return $this->allStandsByAirfield;
    }

    /**
     * Any aircraft that is moving or is in the air, cannot be occupying a stand.
     *
     * @return Collection | NetworkAircraft[]
     */
    private function getAircraftWithOccupiedStandsThatCanNoLongerOccupyThem(): Collection
    {
        return NetworkAircraft::with('occupiedStand')
            ->whereHas('occupiedStand')
            ->where(function (Builder $subquery) {
                $subquery->where('groundspeed', '>', self::MAX_OCCUPANCY_SPEED)
                    ->orWhere('altitude', '>', self::MAX_OCCUPANCY_ALTITUDE);
            })
            ->get();
    }

    /**
     * Get the aircraft that can potentially occupy a different stand
     *
     * We ignore anything that hasn't moved since it was last deemed to occupy a stand because we know its
     * still there.
     *
     * @return Collection | NetworkAircraft[]
     */
    private function getAircraftThatCanOccupyStands(): Collection
    {
        return NetworkAircraft::with('occupiedStand')
            ->leftJoin('aircraft_stand', 'network_aircraft.callsign', '=', 'aircraft_stand.callsign')
            ->where(function (Builder $subquery) {
                // Either they've moved slightly, so we should recheck, or they aren't currently occupying anything.
                $subquery->whereRaw('network_aircraft.latitude <> aircraft_stand.latitude')
                    ->orWhereRaw('network_aircraft.longitude <> aircraft_stand.longitude')
                    ->orWhereNull('aircraft_stand.latitude');
            })
            ->where(function (Builder $subquery) {
                // They not moving
                $subquery->where('groundspeed', '<=', self::MAX_OCCUPANCY_SPEED)
                    ->where('altitude', '<=', self::MAX_OCCUPANCY_ALTITUDE);
            })
            ->select('network_aircraft.*')
            ->get();
    }

    /**
     * Go through every aircraft we're tracking and check whether or not they're occupying a stand.
     */
    public function setOccupiedStands(): void
    {
        $occupiedStandsToRemove = $this->getAircraftWithOccupiedStandsThatCanNoLongerOccupyThem();
        $aircraftThatCanOccupyStands = $this->getAircraftThatCanOccupyStands();
        $standOccupationsToUpdate = new Collection();

        foreach ($aircraftThatCanOccupyStands as $aircraft) {
            // Still occupying that same stand, nothing to do.
            $currentlyOccupiedStand = $aircraft->occupiedStand->first();
            if (
                $currentlyOccupiedStand &&
                $this->standOccupied($aircraft, $currentlyOccupiedStand)
            ) {
                continue;
            }

            $selectedStand = $this->getOccupiedStand($aircraft);
            if ($this->occupiedStandShouldBeRemoved($currentlyOccupiedStand, $selectedStand)) {
                $occupiedStandsToRemove->add($aircraft);
            } elseif ($this->occupiedStandShouldBeUpdated($currentlyOccupiedStand, $selectedStand)) {
                $standOccupationsToUpdate->add(
                    tap(
                        $aircraft,
                        function (NetworkAircraft $aircraft) use ($selectedStand) {
                            $aircraft->standToBeOccupied = $selectedStand;
                        }
                    )
                );
            }
        }

        $this->vacateStands($occupiedStandsToRemove);
        $this->occupyStands($standOccupationsToUpdate);
    }

    /**
     * Vacate stands where required and trigger the vacation event.
     */
    private function vacateStands(Collection $aircraftToVacate): void
    {
        // Delete all the allocations
        DB::table('aircraft_stand')
            ->whereIn('callsign', $aircraftToVacate->pluck('callsign'))
            ->delete();
    }

    /**
     * Occupy the stands that are newly occupied, and remove conflicting assignments.
     */
    private function occupyStands(Collection $standsToOccupy): void
    {
        // Update all the occupations
        $mappedOccupations = $standsToOccupy->map(function (NetworkAircraft $aircraft) {
            return [
                'latitude' => $aircraft->latitude,
                'longitude' => $aircraft->longitude,
                'callsign' => $aircraft->callsign,
                'stand_id' => $aircraft->standToBeOccupied->id,
                'updated_at' => Carbon::now(),
            ];
        });

        // Update the occupations
        DB::table('aircraft_stand')->upsert(
            $mappedOccupations->toArray(),
            ['callsign']
        );

        // Remove the conflicting assignments
        $this->deleteConflictingAssignmentsFollowingOccupation($mappedOccupations);
    }

    /**
     * Given some stands that have been recently occupied, remove any conflicting stand assignments.
     */
    private function deleteConflictingAssignmentsFollowingOccupation(Collection $newOccupations): void
    {
        $conflictingAssignments = StandAssignment::whereNotIn('callsign', $newOccupations->pluck('callsign'))
            ->whereIn('stand_id', $newOccupations->pluck('stand_id'))
            ->get();

        StandAssignment::whereIn('callsign', $conflictingAssignments->pluck('callsign'))
            ->delete();

        $conflictingAssignments->each(function (StandAssignment $assignment) {
            event(new StandUnassignedEvent($assignment->callsign));
        });
    }

    /**
     * Occupied stands should be updated if:
     *
     * 1. There isn't one currently occupied, but they're now occupying one.
     * 2. They were occupying one, but they're now occupying a different one.
     */
    private function occupiedStandShouldBeUpdated(?Stand $currentStand, ?Stand $selectedStand): bool
    {
        return (!$currentStand && $selectedStand) ||
            ($currentStand && $selectedStand && $currentStand->id !== $selectedStand->id);
    }

    /**
     * An occupied stand should be removed, if the aircraft has one, but is no longer actually occupying it.
     */
    private function occupiedStandShouldBeRemoved(?Stand $currentStand, ?Stand $selectedStand): bool
    {
        return ($currentStand && !$selectedStand);
    }

    /**
     * Get the stand that the aircraft is closest to, within occupation parameters.
     */
    private function getOccupiedStand(NetworkAircraft $aircraft): ?Stand
    {
        $selectedStand = null;
        $selectedStandDistance = PHP_INT_MAX;

        foreach ($this->getAllStandsByAirfield() as $airfield) {
            $distanceFromAirfield = $airfield->coordinate->getDistance($aircraft->latLong, new Haversine());
            if ($distanceFromAirfield > self::DISTANCE_FROM_AIRFIELD_TO_CHECK_STANDS) {
                continue;
            }

            foreach ($airfield->stands as $stand) {
                $distanceFromStand = $stand->coordinate->getDistance($aircraft->latLong, new Haversine());
                if (
                    $this->standOccupied($aircraft, $stand) &&
                    $distanceFromStand < $selectedStandDistance
                ) {
                    $selectedStand = $stand;
                    $selectedStandDistance = $distanceFromStand;
                }
            }
        }

        return $selectedStand;
    }

    private function standOccupied(NetworkAircraft $aircraft, Stand $stand): bool
    {
        $distanceFromStand = $stand->coordinate->getDistance($aircraft->latLong, new Haversine());
        return $distanceFromStand < self::MAX_OCCUPANCY_DISTANCE_METERS;
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
            $assignedStand->airfield->code !== $aircraft->planned_destairport &&
            $assignedStand->airfield->code !== $aircraft->planned_depairport
        ) {
            $this->deleteStandAssignmentByCallsign($aircraft->callsign);
        }
    }

    public function getAircraftEligibleForArrivalStandAllocation(): Collection
    {
        return NetworkAircraft::whereIn(
            'planned_destairport',
            Airfield::all()->pluck('code')->toArray()
        )
            ->notTimedOut()
            ->whereDoesntHave('assignedStand')
            ->get();
    }

    /**
     * If two people are on the same stand, then we pick the first one to be there... two people
     * can't be on the same stand. But we don't override an already assigned stand because that just gets messy.
     */
    private function getDepartureStandsToAssign(): Collection
    {
        $aircraftOnStands = NetworkAircraft::join('aircraft_stand', 'network_aircraft.callsign', '=', 'aircraft_stand.callsign')
            ->leftJoin('stand_assignments', 'stand_assignments.callsign', '=', 'network_aircraft.callsign')
            ->orderByRaw('stand_assignments.callsign IS NULL')
            ->orderBy('aircraft_stand.id')
            ->select('network_aircraft.*')
            ->get()
            ->unique(function (NetworkAircraft $aircraft) {
                return $aircraft->occupiedStand->first()->id;
            });

        return NetworkAircraft::join('aircraft_stand', 'network_aircraft.callsign', '=', 'aircraft_stand.callsign')
            ->join('stands', 'aircraft_stand.stand_id', '=', 'stands.id')
            ->join('airfield', 'airfield.id', '=', 'stands.airfield_id')
            ->leftJoin('stand_assignments', 'network_aircraft.callsign', '=', 'stand_assignments.callsign')
            ->where(function (Builder $subquery): void {
                $subquery->whereRaw('aircraft_stand.stand_id <> stand_assignments.stand_id')
                    ->orWhereNull('stand_assignments.stand_id');
            })
            ->whereRaw('airfield.code = network_aircraft.planned_depairport')
            ->whereIn('network_aircraft.callsign', $aircraftOnStands->pluck('callsign'))
            ->select(['network_aircraft.*', 'aircraft_stand.stand_id'])
            ->get();
    }

    private function getDepartureStandsToUnassign(): Collection
    {
        return StandAssignment::join('network_aircraft', 'network_aircraft.callsign', '=', 'stand_assignments.callsign')
            ->join('stands', 'stand_assignments.stand_id', '=', 'stands.id')
            ->join('airfield', 'stands.airfield_id', '=', 'airfield.id')
            ->leftJoin('aircraft_stand', 'network_aircraft.callsign', '=', 'aircraft_stand.callsign')
            ->whereRaw('airfield.code = network_aircraft.planned_depairport')
            ->whereNull('aircraft_stand.callsign')
            ->select('stand_assignments.*')
            ->get();
    }

    /**
     * Assign aircraft to their occupied stand if at their departure airfield. Remove the assignments
     * if they've left the stand.
     */
    public function assignStandsForDeparture(): void
    {
        $this->getDepartureStandsToAssign()->each(function (NetworkAircraft $aircraft) {
            $this->assignStandToAircraft($aircraft->callsign, $aircraft->stand_id);
        });
        $this->getDepartureStandsToUnassign()->each(function (StandAssignment $assignment) {
            $this->deleteStandAssignment($assignment);
        });
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
