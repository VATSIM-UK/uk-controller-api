<?php

namespace App\Jobs\Stand;

use App\BaseFunctionalTestCase;
use App\Events\StandUnassignedEvent;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\NetworkAircraftService;
use Illuminate\Support\Facades\Event;

class TriggerUnassignmentOnDisconnectTest extends BaseFunctionalTestCase
{
    private TriggerUnassignmentOnDisconnect $listener;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
        $this->listener = $this->app->make(TriggerUnassignmentOnDisconnect::class);
    }

    public function testItDeletesStandAssignments()
    {
        $this->addStandAssignment('BAW123', 1);
        Event::assertDispatched([]);
        $this->listener->perform(NetworkAircraft::find('BAW123'));
        Event::assertDispatched(fn(StandUnassignedEvent $event) => $event->getCallsign() === 'BAW123');
        $this->assertNull(StandAssignment::find('BAW123'));
    }

    public function testDoesntFireEventIfNoAssignment()
    {
        Event::assertNotDispatched(fn(StandUnassignedEvent $event) => $event->getCallsign() === 'BAW123');
        $this->listener->perform(NetworkAircraft::find('BAW123'));
    }

    private function addStandAssignment(string $callsign, int $standId): StandAssignment
    {
        NetworkAircraftService::createPlaceholderAircraft($callsign);
        return StandAssignment::create(
            [
                'callsign' => $callsign,
                'stand_id' => $standId,
            ]
        );
    }
}
