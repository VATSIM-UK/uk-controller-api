<?php

namespace App\Listeners\Hold;

use App\BaseUnitTestCase;
use App\Events\HoldUnassignedEvent;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Models\Vatsim\NetworkAircraft;

class UnassignHoldOnDisconnectTest extends BaseUnitTestCase
{
    public function testItDoesNotTriggerAHoldUnassignedEventIfAircraftNotHolding()
    {
        $this->doesntExpectEvents(HoldUnassignedEvent::class);
        $listener = new UnassignHoldOnDisconnect();
        $this->assertFalse($listener->handle(new NetworkAircraftDisconnectedEvent(new NetworkAircraft(['AAL1234']))));
    }

    public function testItTriggersAHoldUnassignedEventIfAircraftIsAssignedHold()
    {
        $this->expectsEvents(HoldUnassignedEvent::class);
        $listener = new UnassignHoldOnDisconnect();
        $this->assertFalse($listener->handle(new NetworkAircraftDisconnectedEvent(new NetworkAircraft(['BAW123']))));
    }

    public function testItDeletesAssignedHold()
    {
        $listener = new UnassignHoldOnDisconnect();
        $this->assertFalse(;$listener->handle(new NetworkAircraftDisconnectedEvent(new NetworkAircraft(['BAW123']))));

        $this->assertDatabaseMissing(
            'assigned_holds',
            [
                'callsign' => 'BAW123'
            ]
        );
    }
}
