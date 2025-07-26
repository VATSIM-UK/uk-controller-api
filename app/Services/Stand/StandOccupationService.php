<?php

namespace App\Services\Stand;

use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Location\Distance\Haversine;

class StandOccupationService
{
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
     * The maximum distance in meters from an airfields centre that we bother
     * checking if an aircraft is sat on a stand. This is about 2.5 nautical miles.
     */
    private const DISTANCE_FROM_AIRFIELD_TO_CHECK_STANDS = 5000;


    private readonly StandAssignmentsService $assignmentsService;
    private readonly AirfieldStandService $airfieldStandService;

    public function __construct(StandAssignmentsService $assignmentsService, AirfieldStandService $airfieldStandService)
    {
        $this->assignmentsService = $assignmentsService;
        $this->airfieldStandService = $airfieldStandService;
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
                // They are not moving
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
     * Given some stands that have been recently occupied, remove any conflicting stand assignments, unless the
     * aircraft has already landed.
     */
    private function deleteConflictingAssignmentsFollowingOccupation(Collection $newOccupations): void
    {
        $conflictingAssignments = StandAssignment::with('aircraft')
            ->whereNotIn('callsign', $newOccupations->pluck('callsign'))
            ->whereIn('stand_id', $newOccupations->pluck('stand_id'))
            ->get();

        foreach ($conflictingAssignments as $assignment) {
            if ($assignment->aircraft && $assignment->aircraft->hasLanded()) {
                continue;
            }
            $this->assignmentsService->deleteStandAssignment($assignment);
        }
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
        return $currentStand && !$selectedStand;
    }

    /**
     * Get the stand that the aircraft is closest to, within occupation parameters.
     */
    public function getOccupiedStand(NetworkAircraft $aircraft): ?Stand
    {
        $selectedStand = null;
        $selectedStandDistance = PHP_INT_MAX;

        foreach ($this->airfieldStandService->getAllStandsByAirfield() as $airfield) {
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
}
