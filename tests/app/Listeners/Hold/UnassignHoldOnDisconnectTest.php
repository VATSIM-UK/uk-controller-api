<?php

namespace App\Listeners\Hold;

use App\BaseUnitTestCase;
use App\Events\HoldUnassignedEvent;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Models\Vatsim\NetworkAircraft;

class UnassignHoldOnDisconnectTest extends BaseUnitTestCase
{
    public function testItTriggersAHoldUnassignedEvent()
    {
        $this->expectsEvents(HoldUnassignedEvent::class);
        $event =  new UnassignHoldOnDisconnect();
        $event->handle(new NetworkAircraftDisconnectedEvent(new NetworkAircraft(['BAW123'])));
    }
}
