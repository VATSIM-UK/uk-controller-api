<?php


namespace App\Listeners\Hold;

use App\Events\HoldUnassignedEvent;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Models\Vatsim\NetworkAircraft;

class UnassignHoldOnDisconnect
{
    public function handle(NetworkAircraftDisconnectedEvent $event) : bool
    {
        event(new HoldUnassignedEvent($event->getAircraft()));
        return false;
    }
}
