<?php

namespace App\Jobs\Stand;

use App\BaseFunctionalTestCase;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Events\StandUnassignedEvent;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\NetworkDataService;

class TriggerUnassignmentOnDisconnectTest extends BaseFunctionalTestCase
{
    private TriggerUnassignmentOnDisconnect $listener;

    public function setUp() : void
    {
        parent::setUp();
        $this->listener = new TriggerUnassignmentOnDisconnect(NetworkAircraft::find('BAW123'));
    }

    public function testItFiresEventIfStandAssignmentExists()
    {
        $this->addStandAssignment('BAW123', 1);
        $this->expectsEvents(StandUnassignedEvent::class);
        $this->listener->handle();
    }

    public function testItDeletesStandAssignments()
    {
        $this->addStandAssignment('BAW123', 1);
        $this->expectsEvents([]);
        $this->listener->handle();

        $this->assertNull(StandAssignment::find('BAW123'));
    }

    public function testDoesntFireEventIfNoAssignment()
    {
        $this->doesntExpectEvents(StandUnassignedEvent::class);
        $this->listener->handle();
    }

    private function addStandAssignment(string $callsign, int $standId): StandAssignment
    {
        NetworkDataService::firstOrCreateNetworkAircraft($callsign);
        return StandAssignment::create(
            [
                'callsign' => $callsign,
                'stand_id' => $standId,
            ]
        );
    }
}
