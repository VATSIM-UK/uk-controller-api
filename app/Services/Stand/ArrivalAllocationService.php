<?php

namespace App\Services\Stand;

use App\Models\Stand\StandAssignment;

class ArrivalAllocationService
{
    private readonly StandAssignmentsService $assignmentsService;

    public function __construct(StandAssignmentsService $assignmentsService)
    {
        $this->assignmentsService = $assignmentsService;
    }

    private function deleteAssignmentsForAircraftWithChangedDestination(): void
    {
        StandAssignment::join('network_aircraft', 'network_aircraft.callsign', '=', 'stand_assignments.callsign')
            ->join('stands', 'stands.id', '=', 'stand_assignments.stand_id')
            ->join('airfield', 'airfield.id', '=', 'stands.airfield_id')
            ->whereRaw('airfield.code <> network_aircraft.planned_destairport')
            ->whereRaw('airfield.code <> network_aircraft.planned_depairport')
            ->select('stand_assignments.*')
            ->get()
            ->each(function (StandAssignment $standAssignment) {
                $this->assignmentsService->deleteStandAssignment($standAssignment);
            });
    }

    public function allocateStandsAtArrivalAirfields(): void
    {
        $this->deleteAssignmentsForAircraftWithChangedDestination();
    }
}
