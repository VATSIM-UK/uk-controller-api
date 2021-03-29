<?php

namespace App\Jobs\Hold;

use App\BaseFunctionalTestCase;
use App\Events\HoldUnassignedEvent;
use App\Models\Hold\AssignedHold;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;

class UnassignHoldOnDisconnectTest extends BaseFunctionalTestCase
{

    private UnassignHoldOnDisconnect $listener;

    public function setUp() : void
    {
        parent::setUp();
        $this->listener = new UnassignHoldOnDisconnect(NetworkAircraft::find('BAW123'));
        Carbon::setTestNow(Carbon::now());
    }

    public function testItDoesNotTriggerAHoldUnassignedEventIfAircraftNotHolding()
    {
        AssignedHold::where('callsign', 'BAW123')->delete();
        $this->doesntExpectEvents(HoldUnassignedEvent::class);
        $this->listener->handle();
    }

    public function testItTriggersAHoldUnassignedEventIfAircraftIsAssignedHold()
    {
        $this->expectsEvents(HoldUnassignedEvent::class);
        $this->listener->handle();
    }

    public function testItDeletesAssignedHold()
    {
        $this->withoutEvents();
        $this->listener->handle();

        $this->assertDatabaseMissing(
            'assigned_holds',
            [
                'callsign' => 'BAW123',
            ]
        );
    }
}
