<?php

namespace App\Jobs\Hold;

use App\BaseFunctionalTestCase;
use App\Events\HoldUnassignedEvent;
use App\Models\Hold\AssignedHold;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

class UnassignHoldOnDisconnectTest extends BaseFunctionalTestCase
{

    private UnassignHoldOnDisconnect $listener;

    public function setUp(): void
    {
        parent::setUp();
        $this->listener = $this->app->make(UnassignHoldOnDisconnect::class);
        Carbon::setTestNow(Carbon::now());
        Event::fake();
    }

    public function testItDoesNotTriggerAHoldUnassignedEventIfAircraftNotHolding()
    {
        AssignedHold::where('callsign', 'BAW123')->delete();
        Event::assertNotDispatched(HoldUnassignedEvent::class);
        $this->listener->perform(NetworkAircraft::find('BAW123'));
    }

    public function testItTriggersAHoldUnassignedEventIfAircraftIsAssignedHold()
    {
        Event::assertDispatched(HoldUnassignedEvent::class);
        $this->listener->perform(NetworkAircraft::find('BAW123'));
    }

    public function testItDeletesAssignedHold()
    {
        Event::fake();
        $this->listener->perform(NetworkAircraft::find('BAW123'));

        $this->assertDatabaseMissing(
            'assigned_holds',
            [
                'callsign' => 'BAW123',
            ]
        );
    }
}
