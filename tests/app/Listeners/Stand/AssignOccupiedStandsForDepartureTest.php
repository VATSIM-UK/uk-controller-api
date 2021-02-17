<?php

namespace App\Listeners\Stand;

use App\BaseFunctionalTestCase;
use App\Events\StandOccupiedEvent;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Services\NetworkDataService;
use App\Services\StandService;
use Mockery;
use Mockery\MockInterface;

class AssignOccupiedStandsForDepartureTest extends BaseFunctionalTestCase
{
    private MockInterface $serviceMock;
    private AssignOccupiedStandsForDeparture $listener;

    public function setUp(): void
    {
        parent::setUp();
        $this->serviceMock = Mockery::mock(StandService::class);
        $this->app->instance(StandService::class, $this->serviceMock);
        $this->listener = $this->app->make(AssignOccupiedStandsForDeparture::class);
    }

    private function expectAssignment()
    {
        $this->serviceMock->shouldReceive('assignStandToAircraft')
            ->with('BAW123', 1)
            ->once();
    }

    private function expectNoAssignment()
    {
        $this->serviceMock->shouldNotReceive('assignStandToAircraft');
    }

    public function testItAssignsStandsForDepartingAircraft()
    {
        $this->expectAssignment();
        $event = new StandOccupiedEvent(
            NetworkDataService::createOrUpdateNetworkAircraft(
                'BAW123',
                [
                    'planned_depairport' => 'EGLL',
                ]
            ),
            Stand::find(1)
        );
        $this->listener->handle($event);
    }

    public function testItAssignsStandsForDepartingAircraftWithNoFlightplan()
    {
        $this->expectAssignment();
        $event = new StandOccupiedEvent(
            NetworkDataService::createOrUpdateNetworkAircraft(
                'BAW123',
                [
                    'planned_depairport' => null,
                ]
            ),
            Stand::find(1)
        );
        $this->listener->handle($event);
    }

    public function testItAssignsStandsForDepartingAircraftWhereAircraftHasMoved()
    {
        StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 2,
            ]
        );

        $this->expectAssignment();
        $event = new StandOccupiedEvent(
            NetworkDataService::createOrUpdateNetworkAircraft(
                'BAW123',
                [
                    'planned_depairport' => 'EGLL',
                ]
            ),
            Stand::find(1)
        );
        $this->listener->handle($event);
    }

    public function testItDoesntAssignStandIfNotDeparting()
    {
        $this->expectNoAssignment();
        $event = new StandOccupiedEvent(
            NetworkDataService::createOrUpdateNetworkAircraft(
                'BAW123',
                [
                    'planned_depairport' => 'EGGD',
                ]
            ),
            Stand::find(1)
        );
        $this->listener->handle($event);
    }

    public function testItDoesntAssignStandIfAircraftAlreadyAssignedToStand()
    {
        StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );

        $this->expectNoAssignment();
        $event = new StandOccupiedEvent(
            NetworkDataService::createOrUpdateNetworkAircraft(
                'BAW123',
                [
                    'planned_depairport' => 'EGLL',
                ]
            ),
            Stand::find(1)
        );
        $this->listener->handle($event);
    }
}
