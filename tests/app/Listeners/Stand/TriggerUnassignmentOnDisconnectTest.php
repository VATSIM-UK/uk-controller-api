<?php

namespace App\Listeners\Stand;

use App\BaseFunctionalTestCase;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Events\StandUnassignedEvent;
use App\Models\Vatsim\NetworkAircraft;

class TriggerUnassignmentOnDisconnectTest extends BaseFunctionalTestCase
{
    /**
     * @var TriggerUnassignmentOnDisconnect
     */
    private $listener;

    public function setUp() : void
    {
        parent::setUp();
        $this->listener = $this->app->make(TriggerUnassignmentOnDisconnect::class);
    }

    public function testItFiresEvents()
    {
        $this->expectsEvents(StandUnassignedEvent::class);
        $this->listener->handle(new NetworkAircraftDisconnectedEvent(new NetworkAircraft(['callsign' => 'BAW123'])));
    }

    public function testItContinuesPropagation()
    {
        $this->expectsEvents([]);
        $this->assertTrue(
            $this->listener->handle(new NetworkAircraftDisconnectedEvent(new NetworkAircraft(['callsign' => 'BAW123'])))
        );
    }
}
