<?php

namespace App\Listeners\Stand;

use App\BaseFunctionalTestCase;
use App\Events\NetworkAircraftUpdatedEvent;
use App\Events\StandUnassignedEvent;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\NetworkDataService;

class TriggerDepartureUnassignmentOnceAirborneTest extends BaseFunctionalTestCase
{
    /**
     * @var MarkAssignmentDeletedOnUnassignment
     */
    private $listener;

    public function setUp() : void
    {
        parent::setUp();
        $this->listener = $this->app->make(TriggerDepartureUnassignmentOnceAirborne::class);
        $this->event = new NetworkAircraftUpdatedEvent(NetworkAircraft::find('BAW123'));
    }

    public function testItDoesNothingIfGoingTooSlow()
    {
        $this->doesntExpectEvents(StandUnassignedEvent::class);
        $this->addStandAssignment('BAW123', 1);
        NetworkAircraft::where('callsign', 'BAW123')->update(['groundspeed' => 49, 'planned_depairport' => 'EGLL']);
        $event = new NetworkAircraftUpdatedEvent(NetworkAircraft::find('BAW123'));

        $this->assertTrue($this->listener->handle($event));
        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'BAW123',
                'stand_id' => 1.
            ]
        );
    }

    public function testItDoesNothingIfTooLow()
    {
        $this->doesntExpectEvents(StandUnassignedEvent::class);
        $this->addStandAssignment('BAW123', 1);
        NetworkAircraft::where('callsign', 'BAW123')->update(['altitude' => 999, 'planned_depairport' => 'EGLL']);
        $event = new NetworkAircraftUpdatedEvent(NetworkAircraft::find('BAW123'));

        $this->assertTrue($this->listener->handle($event));
        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'BAW123',
                'stand_id' => 1.
            ]
        );
    }

    public function testItDoesNothingIfStandAssignmentForDifferentAirport()
    {
        $this->doesntExpectEvents(StandUnassignedEvent::class);
        $this->addStandAssignment('BAW123', 1);
        NetworkAircraft::where('callsign', 'BAW123')->update(['planned_depairport' => 'EGBB']);
        $event = new NetworkAircraftUpdatedEvent(NetworkAircraft::find('BAW123'));

        $this->assertTrue($this->listener->handle($event));
        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'BAW123',
                'stand_id' => 1.
            ]
        );
    }

    public function testItDeletesDepartureAirportStandAssignmentOnceAirborne()
    {
        $this->expectsEvents(StandUnassignedEvent::class);
        $this->addStandAssignment('BAW123', 1);
        NetworkAircraft::where('callsign', 'BAW123')->update(['planned_depairport' => 'EGLL']);
        $event = new NetworkAircraftUpdatedEvent(NetworkAircraft::find('BAW123'));

        $this->assertTrue($this->listener->handle($event));
        $this->assertDatabaseMissing(
            'stand_assignments',
            [
                'callsign' => 'BAW123'
            ]
        );
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
