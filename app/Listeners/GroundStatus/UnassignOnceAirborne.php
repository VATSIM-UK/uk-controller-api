<?php

namespace App\Listeners\GroundStatus;

use App\Events\GroundStatusUnassignedEvent;
use App\Events\NetworkAircraftUpdatedEvent;

class UnassignOnceAirborne
{
    const MIN_ALTITUDE = 1000;
    const MIN_GROUNDSPEED = 65;

    public function handle(NetworkAircraftUpdatedEvent $event) : bool
    {
        if (!$event->getAircraft()->groundStatus->first()) {
            return true;
        }

        // Remove the ground status if the aircraft is high enough or travelling fast enough
        if (
            $event->getAircraft()->groundspeed >= self::MIN_GROUNDSPEED ||
            $event->getAircraft()->altitude >= self::MIN_ALTITUDE
        ) {
            $event->getAircraft()->groundStatus()->detach();
            event(new GroundStatusUnassignedEvent($event->getAircraft()->callsign));
        }

        return true;
    }
}
