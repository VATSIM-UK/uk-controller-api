<?php

namespace App\Listeners\Stand;

use App\Events\NetworkAircraftDisconnectedEvent;
use App\Events\StandUnassignedEvent;

class TriggerUnassignmentOnDisconnect
{
    public function handle(NetworkAircraftDisconnectedEvent $event) : bool
    {
        event(new StandUnassignedEvent($event->getAircraft()->callsign));
        return true;
    }
}
