<?php

namespace App\Listeners\Hold;

use App\BaseFunctionalTestCase;
use App\BaseUnitTestCase;
use App\Events\HoldUnassignedEvent;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Models\Vatsim\NetworkAircraft;

class UnassignHoldOnDisconnectTest extends BaseFunctionalTestCase
{
    public function testItDoesNotTriggerAHoldUnassignedEventIfAircraftNotHolding()
    {
        $this->doesntExpectEvents(HoldUnassignedEvent::class);
        $listener = new UnassignHoldOnDisconnect();
        $this->assertTrue(
            $listener->handle(new NetworkAircraftDisconnectedEvent(new NetworkAircraft(['callsign' => 'AAL1234'])))
        );
    }

    public function testItTriggersAHoldUnassignedEventIfAircraftIsAssignedHold()
    {
        $this->expectsEvents(HoldUnassignedEvent::class);
        $listener = new UnassignHoldOnDisconnect();
        $this->assertTrue(
            $listener->handle(new NetworkAircraftDisconnectedEvent(new NetworkAircraft(['callsign' => 'BAW123'])))
        );
    }
}
