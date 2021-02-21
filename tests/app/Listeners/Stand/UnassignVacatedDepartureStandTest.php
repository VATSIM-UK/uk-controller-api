<?php

namespace App\Listeners\Stand;

use App\BaseFunctionalTestCase;
use App\Events\StandVacatedEvent;
use App\Models\Stand\StandAssignment;
use App\Services\NetworkDataService;

class UnassignVacatedDepartureStandTest extends BaseFunctionalTestCase
{
    private UnassignVacatedDepartureStand $listener;

    public function setUp(): void
    {
        parent::setUp();
        $this->listener = $this->app->make(UnassignVacatedDepartureStand::class);
        $this->withoutEvents();
    }

    public function testItUnassignsStandsForDepartingAircraft()
    {
        $event = new StandVacatedEvent(
            NetworkDataService::createOrUpdateNetworkAircraft(
                'BAW123',
                [
                    'planned_depairport' => 'EGLL',
                ]
            )
        );
        StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );

        $this->assertTrue($this->listener->handle($event));
        $this->assertNull(StandAssignment::find('BAW123'));
    }

    public function testItDoesntUnassignIfAssignmentAtDifferentAirport()
    {
        $event = new StandVacatedEvent(
            NetworkDataService::createOrUpdateNetworkAircraft(
                'BAW123',
                [
                    'planned_depairport' => 'EGBB',
                ]
            )
        );
        StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );

        $this->assertTrue($this->listener->handle($event));
        $this->assertNotNull(StandAssignment::find('BAW123'));
    }

    public function testItHandlesNothingToUnassign()
    {
        $event = new StandVacatedEvent(
            NetworkDataService::createOrUpdateNetworkAircraft(
                'BAW123',
                [
                    'planned_depairport' => 'EGBB',
                ]
            )
        );
        $this->assertTrue($this->listener->handle($event));
    }
}
