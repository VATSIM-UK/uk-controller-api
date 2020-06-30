<?php


namespace App\Listeners\Hold;

use App\Events\HoldUnassignedEvent;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Models\Hold\AssignedHold;
use App\Models\Vatsim\NetworkAircraft;

class UnassignHoldOnDisconnect
{
    public function handle(NetworkAircraftDisconnectedEvent $event) : bool
    {
        $assignedHold = AssignedHold::find($event->getAircraft()->callsign);
        if (!$assignedHold) {
            return;
        }

        $assignedHold->delete();
        event(new HoldUnassignedEvent($event->getAircraft()->callsign));
        return false;
    }
}
