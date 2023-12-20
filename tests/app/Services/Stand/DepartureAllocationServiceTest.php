<?php

namespace App\Services\Stand;

use App\BaseFunctionalTestCase;
use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Services\NetworkAircraftService;
use Illuminate\Support\Facades\Event;
use Mockery;
use Mockery\Mock;

class DepartureAllocationServiceTest extends BaseFunctionalTestCase
{
    private readonly DepartureAllocationService $service;
    private readonly StandOccupationService $occupationService;

    public function setUp(): void
    {
        parent::setUp();
        $this->occupationService = Mockery::mock(StandOccupationService::class);
        $this->app->instance(StandOccupationService::class, $this->occupationService);
        $this->service = $this->app->make(DepartureAllocationService::class);
        Event::fake();
    }

    public function testItAssignsOccupiedStandsAtDepartureAirfields()
    {
        $aircraft = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 0,
                'altitude' => 0,
                'planned_depairport' => 'EGLL',
            ]
        );
        $aircraft->occupiedStand()->sync([2]);
        $this->service->assignStandsForDeparture();

        $this->assertTrue(StandAssignment::where('callsign', 'RYR787')->where('stand_id', 2)->exists());
        Event::assertDispatched(StandAssignedEvent::class);
    }

    public function testItUpdatesAssignedOccupiedStandsAtDepartureAirfields()
    {
        $aircraft = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 0,
                'altitude' => 0,
                'planned_depairport' => 'EGLL',
            ]
        );
        $aircraft->occupiedStand()->sync([2]);
        $this->addStandAssignment('RYR787', 1);

        $this->service->assignStandsForDeparture();

        $this->assertTrue(StandAssignment::where('callsign', 'RYR787')->where('stand_id', 2)->exists());
        $this->assertFalse(StandAssignment::where('callsign', 'RYR787')->where('stand_id', 1)->exists());
        Event::assertDispatched(StandAssignedEvent::class);
    }

    public function testItDoesntSupersedeAnExistingDepartureAssignment()
    {
        $aircraft1 = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BAW123',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 0,
                'altitude' => 0,
                'planned_depairport' => 'EGLL',
            ]
        );
        $aircraft1->occupiedStand()->sync([2]);

        // RYR787 already has the stand assigned, so BAW123 should not supersede it.
        $aircraft2 = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 0,
                'altitude' => 0,
                'planned_depairport' => 'EGLL',
            ]
        );
        $aircraft2->occupiedStand()->sync([2]);
        $this->addStandAssignment('RYR787', 2);

        $this->service->assignStandsForDeparture();

        $this->assertTrue(StandAssignment::where('callsign', 'RYR787')->where('stand_id', 2)->exists());
        $this->assertFalse(StandAssignment::where('callsign', 'BAW123')->exists());
        Event::assertNotDispatched(StandAssignedEvent::class);
    }

    public function testItDoesntAssignStandsAtNonDepartureAirfields()
    {
        $aircraft = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 0,
                'altitude' => 0,
                'planned_depairport' => 'EGBB',
            ]
        );
        $aircraft->occupiedStand()->sync([2]);
        $this->service->assignStandsForDeparture();

        $this->assertFalse(StandAssignment::where('callsign', 'RYR787')->exists());
        Event::assertNotDispatched(StandAssignedEvent::class);
    }

    public function testItRemovesAssignmentsAtDepartureAirfieldIfStandUnoccupied()
    {
        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 0,
                'altitude' => 0,
                'planned_depairport' => 'EGLL',
            ]
        );
        $this->addStandAssignment('RYR787', 1);

        $this->service->assignStandsForDeparture();
        $this->assertFalse(StandAssignment::where('callsign', 'RYR787')->exists());
        Event::assertDispatched(StandUnassignedEvent::class);
    }

    public function testItDoesntRemoveStandAssignmentsIfNoStandOccupiedButAssignmentNotAtDepartureAirfield()
    {
        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 0,
                'altitude' => 0,
                'planned_depairport' => 'EGBB',
            ]
        );
        $this->addStandAssignment('RYR787', 1);

        $this->service->assignStandsForDeparture();
        $this->assertTrue(StandAssignment::where('callsign', 'RYR787')->exists());
        Event::assertNotDispatched(StandUnassignedEvent::class);
    }

    public function testItDoesntAssignStandsToDepartingAircraftIfNotOccupying()
    {
        $aircraft = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 0,
                'altitude' => 0,
                'planned_depairport' => 'EGBB',
            ]
        );

        $this->occupationService->shouldReceive('getOccupiedStand')->with($aircraft)->andReturn(null);
        $this->service->assignStandToDepartingAircraft($aircraft);

        Event::assertNotDispatched(StandAssignedEvent::class);
    }

    public function testItAssignsOccupiedStandToDepartingAircraft()
    {
        $aircraft = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 0,
                'altitude' => 0,
                'planned_depairport' => 'EGBB',
            ]
        );

        $this->occupationService->shouldReceive('getOccupiedStand')->with($aircraft)->andReturn(Stand::find(1));
        $this->service->assignStandToDepartingAircraft($aircraft);

        $this->assertDatabaseHas
        (
            'stand_assignments',
            [
                'callsign' => 'RYR787',
                'stand_id' => 1,
            ]
        );
        Event::assertDispatched(StandAssignedEvent::class);
    }

    private function addStandAssignment(string $callsign, int $standId): void
    {
        NetworkAircraftService::createPlaceholderAircraft($callsign);
        StandAssignment::create(
            [
                'callsign' => $callsign,
                'stand_id' => $standId,
            ]
        );
    }
}
