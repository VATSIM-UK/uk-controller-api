<?php

namespace App\Listeners\Hold;

use App\Events\HoldUnassignedEvent;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Listeners\HighPriority;
use App\Models\Hold\AssignedHold;

class UnassignHoldOnDisconnect
{
    public function handle(NetworkAircraftDisconnectedEvent $event) : bool
    {
        $callsign = $event->getAircraft()->callsign;
        if (AssignedHold::destroy($callsign)) {
            event(new HoldUnassignedEvent($callsign));
        }

        return true;
    }
}
