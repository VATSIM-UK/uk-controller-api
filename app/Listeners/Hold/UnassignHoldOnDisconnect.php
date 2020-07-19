<?php

namespace App\Listeners\Hold;

use App\Events\HoldUnassignedEvent;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Models\Hold\AssignedHold;

class UnassignHoldOnDisconnect
{
    public function handle(NetworkAircraftDisconnectedEvent $event) : bool
    {
        if (AssignedHold::find($event->getAircraft()->callsign)) {
            event(new HoldUnassignedEvent($event->getAircraft()->callsign));
        }

        return true;
    }
}
