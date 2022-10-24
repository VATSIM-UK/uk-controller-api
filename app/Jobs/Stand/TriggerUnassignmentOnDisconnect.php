<?php

namespace App\Jobs\Stand;

use App\Jobs\Network\AircraftDisconnectedSubtask;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\Stand\StandAssignmentsService;

class TriggerUnassignmentOnDisconnect implements AircraftDisconnectedSubtask
{
    private readonly StandAssignmentsService $assignmentsService;

    public function __construct(StandAssignmentsService $assignmentsService)
    {
        $this->assignmentsService = $assignmentsService;
    }

    public function perform(NetworkAircraft $aircraft): void
    {
        $this->assignmentsService->deleteAssignmentIfExists($aircraft);
    }
}
