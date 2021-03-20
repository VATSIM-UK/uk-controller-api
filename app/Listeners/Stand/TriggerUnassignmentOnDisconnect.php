<?php

namespace App\Listeners\Stand;

use App\Events\NetworkAircraftDisconnectedEvent;
use App\Events\StandUnassignedEvent;
use App\Listeners\HighPriority;
use App\Models\Stand\StandAssignment;

class TriggerUnassignmentOnDisconnect
{
    use HighPriority;

    public function handle(NetworkAircraftDisconnectedEvent $event) : bool
    {
        $callsign = $event->getAircraft()->callsign;

        if (StandAssignment::destroy($callsign)) {
            event(new StandUnassignedEvent($callsign));
        }

        return true;
    }
}
