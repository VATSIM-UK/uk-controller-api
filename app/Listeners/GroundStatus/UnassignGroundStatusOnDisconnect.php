<?php

namespace App\Listeners\GroundStatus;

use App\Events\GroundStatusUnassignedEvent;
use App\Events\NetworkAircraftDisconnectedEvent;

class UnassignGroundStatusOnDisconnect
{
    public function handle(NetworkAircraftDisconnectedEvent $event) : bool
    {
        $groundStatus = $event->getAircraft()->groundStatus()->first();

        if ($groundStatus) {
            event(new GroundStatusUnassignedEvent($event->getAircraft()->callsign));
        }

        return true;
    }
}
