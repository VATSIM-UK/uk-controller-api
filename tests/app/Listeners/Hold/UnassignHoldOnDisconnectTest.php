<?php

namespace App\Listeners\Hold;

use App\BaseUnitTestCase;
use App\Events\HoldUnassignedEvent;
use App\Models\Vatsim\NetworkAircraft;

class UnassignHoldOnDisconnectTest extends BaseUnitTestCase
{
    public function testItTriggersAHoldUnassignedEvent()
    {
        $this->expectsEvents(HoldUnassignedEvent::class);
        $event =  new UnassignHoldOnDisconnect();
        $event->handle(new NetworkAircraft(['BAW123']));
    }
}
