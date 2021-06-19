<?php

namespace App\Jobs\Stand;

use App\BaseFunctionalTestCase;
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
        $this->listener = $this->app->make(TriggerUnassignmentOnDisconnect::class);
    }

    public function testItFiresEventIfStandAssignmentExists()
    {
        $this->addStandAssignment('BAW123', 1);
        $this->expectsEvents(StandUnassignedEvent::class);
        $this->listener->perform(NetworkAircraft::find('BAW123'));
    }

    public function testItDeletesStandAssignments()
    {
        $this->addStandAssignment('BAW123', 1);
        $this->expectsEvents([]);
        $this->listener->perform(NetworkAircraft::find('BAW123'));

        $this->assertNull(StandAssignment::find('BAW123'));
    }

    public function testDoesntFireEventIfNoAssignment()
    {
        $this->doesntExpectEvents(StandUnassignedEvent::class);
        $this->listener->perform(NetworkAircraft::find('BAW123'));
    }

    private function addStandAssignment(string $callsign, int $standId): StandAssignment
    {
        NetworkDataService::createPlaceholderAircraft($callsign);
        return StandAssignment::create(
            [
                'callsign' => $callsign,
                'stand_id' => $standId,
            ]
        );
    }
}
