<?php

namespace App\Jobs\Stand;

use App\Events\StandUnassignedEvent;
use App\Jobs\Network\AircraftDisconnectedSubtask;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;

class TriggerUnassignmentOnDisconnect implements AircraftDisconnectedSubtask
{
    public function perform(NetworkAircraft $aircraft): void
    {
        $callsign = $aircraft->callsign;

        if (StandAssignment::destroy($callsign)) {
            event(new StandUnassignedEvent($callsign));
        }
    }
}
