<?php

namespace App\Jobs\Squawk;

use App\Jobs\Network\AircraftDisconnectedSubtask;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\SquawkService;

class MarkAssignmentDeletedOnDisconnect implements AircraftDisconnectedSubtask
{
    private SquawkService $squawkService;

    public function __construct(SquawkService $squawkService)
    {
        $this->squawkService = $squawkService;
    }

    public function perform(NetworkAircraft $aircraft): void
    {
        $this->squawkService->deleteSquawkAssignment($aircraft->callsign);
    }
}
