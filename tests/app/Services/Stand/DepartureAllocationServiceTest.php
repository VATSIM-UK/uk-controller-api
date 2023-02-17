<?php

namespace App\Services\Stand;

use App\BaseFunctionalTestCase;
use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Models\Stand\StandAssignment;
use App\Services\NetworkAircraftService;
use Illuminate\Support\Facades\Event;

class DepartureAllocationServiceTest extends BaseFunctionalTestCase
{
    private readonly DepartureAllocationService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(DepartureAllocationService::class);
        Event::fake();
    }

    public function testItAssignsOccupiedStandsAtDepartureAirfields()
    {
        Event::assertDispatched(StandAssignedEvent::class);
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
    }

    public function testItUpdatesAssignedOccupiedStandsAtDepartureAirfields()
    {
        Event::assertDispatched(StandAssignedEvent::class);
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
    }

    public function testItDoesntSupersedeAnExistingDepartureAssignment()
    {
        Event::assertNotDispatched(StandAssignedEvent::class);
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
    }

    public function testItDoesntAssignStandsAtNonDepartureAirfields()
    {
        Event::assertNotDispatched(StandAssignedEvent::class);
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
    }

    public function testItRemovesAssignmentsAtDepartureAirfieldIfStandUnoccupied()
    {
        Event::assertDispatched(StandUnassignedEvent::class);
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
    }

    public function testItDoesntRemoveStandAssignmentsIfNoStandOccupiedButAssignmentNotAtDepartureAirfield()
    {
        Event::assertNotDispatched(StandUnassignedEvent::class);
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
