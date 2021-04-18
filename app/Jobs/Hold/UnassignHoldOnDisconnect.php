<?php

namespace App\Jobs\Hold;

use App\Events\HoldUnassignedEvent;
use App\Jobs\Network\AircraftDisconnectedSubtask;
use App\Models\Hold\AssignedHold;
use App\Models\Vatsim\NetworkAircraft;

class UnassignHoldOnDisconnect implements AircraftDisconnectedSubtask
{
    public function perform(NetworkAircraft $aircraft): void
    {
        $callsign = $aircraft->callsign;
        if (AssignedHold::destroy($callsign)) {
            event(new HoldUnassignedEvent($callsign));
        }
    }
}
