<?php

namespace App\Services\Stand;

use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DepartureAllocationService
{
    private readonly StandAssignmentsService $assignmentsService;
    private readonly StandOccupationService $standOccupationService;

    public function __construct(
        StandAssignmentsService $assignmentsService,
        StandOccupationService $standOccupationService
    ) {
        $this->assignmentsService = $assignmentsService;
        $this->standOccupationService = $standOccupationService;
    }

    /**
     * Remove assignments for aircraft that are no longer occupying their departure stand, then assign stands
     * for those occupying stands.
     */
    public function assignStandsForDeparture(): void
    {
        $this->getDepartureStandsToUnassign()->each(function (StandAssignment $assignment)
        {
            $this->assignmentsService->deleteStandAssignment($assignment);
        });

        $this->getDepartureStandsToAssign()->each(function (NetworkAircraft $aircraft)
        {
            $this->assignmentsService->createStandAssignment($aircraft->callsign, $aircraft->stand_id, 'Departure');
        });
    }

    public function assignStandToDepartingAircraft(NetworkAircraft $aircraft): ?int
    {
        $occupiedStand = $this->standOccupationService->getOccupiedStand($aircraft);
        if ($occupiedStand === null) {
            return null;
        }

        $this->assignmentsService->createStandAssignment($aircraft->callsign, $occupiedStand->id, 'Departure');
        return $occupiedStand->id;
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
            ->unique(function (NetworkAircraft $aircraft)
            {
                return $aircraft->occupiedStand->first()->id;
            });

        return NetworkAircraft::join('aircraft_stand', 'network_aircraft.callsign', '=', 'aircraft_stand.callsign')
            ->join('stands', 'aircraft_stand.stand_id', '=', 'stands.id')
            ->join('airfield', 'airfield.id', '=', 'stands.airfield_id')
            ->leftJoin('stand_assignments', 'network_aircraft.callsign', '=', 'stand_assignments.callsign')
            ->where(function (Builder $subquery): void
            {
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
}
