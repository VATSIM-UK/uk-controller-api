<?php

namespace App\Services\Stand;

use App\BaseFunctionalTestCase;
use App\Events\StandUnassignedEvent;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Services\NetworkAircraftService;
use Illuminate\Support\Facades\Event;

class StandOccupationServiceTest extends BaseFunctionalTestCase
{
    private readonly StandOccupationService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(StandOccupationService::class);
    }

    public function testItDoesntOccupyStandsIfAircraftTooHigh()
    {
        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 10,
                'altitude' => 751
            ]
        );

        $this->service->setOccupiedStands();
        $this->assertDatabaseMissing(
            'aircraft_stand',
            [
                'callsign' => 'RYR787',
            ]
        );
    }

    public function testItRemovesOccupiedStandIfAircraftTooHigh()
    {
        $aircraft = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 0,
                'altitude' => 751
            ]
        );
        $aircraft->occupiedStand()->sync([1]);

        $this->service->setOccupiedStands();
        $this->assertDatabaseMissing(
            'aircraft_stand',
            [
                'callsign' => 'RYR787',
            ]
        );
    }

    public function testItDoesntOccupyStandsIfAircraftTooFast()
    {
        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 6,
                'altitude' => 0
            ]
        );

        $this->service->setOccupiedStands();
        $this->assertDatabaseMissing(
            'aircraft_stand',
            [
                'callsign' => 'RYR787',
            ]
        );
    }

    public function testItRemovesOccupiedStandIfAircraftTooFast()
    {
        $aircraft = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 11,
                'altitude' => 0
            ]
        );

        $aircraft->occupiedStand()->sync([1]);
        $this->service->setOccupiedStands();
        $this->assertDatabaseMissing(
            'aircraft_stand',
            [
                'callsign' => 'RYR787',
            ]
        );
    }

    public function testItOccupiesAFreshStand()
    {
        $aircraft = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 0,
                'altitude' => 0
            ]
        );
        $this->service->setOccupiedStands();
        $aircraft->refresh();
        $this->assertEquals(2, $aircraft->occupiedStand->first()->id);
    }

    public function testItHandlesCurrentStandIfStillOccupied()
    {
        $aircraft = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 0,
                'altitude' => 0
            ]
        );
        $aircraft->occupiedStand()->sync([2]);
        $this->service->setOccupiedStands();
        $aircraft->refresh();
        $this->assertEquals(2, $aircraft->occupiedStand->first()->id);
    }

    public function testItDoesntChangeOccupiedStandIfTheAircraftHasMovedLatitudeSinceOccupancy()
    {
        $aircraft = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 0,
                'altitude' => 0
            ]
        );
        $aircraft->occupiedStand()->sync([1 => ['latitude' => 51.47187222, 'longitude' => -0.48601389]]);

        $this->service->setOccupiedStands();
        $aircraft->refresh();
        $this->assertEquals(1, $aircraft->occupiedStand->first()->id);
    }

    public function testItChangesOccupiedStandIfTheAircraftHasMovedLatitudeSinceOccupancy()
    {
        $aircraft = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 0,
                'altitude' => 0
            ]
        );
        $aircraft->occupiedStand()->sync([1 => ['latitude' => 53.65883639, 'longitude' => -0.48601389]]);

        $this->service->setOccupiedStands();
        $aircraft->refresh();
        $this->assertEquals(2, $aircraft->occupiedStand->first()->id);
    }

    public function testItChangesOccupiedStandIfTheAircraftHasMovedLongitudeSinceOccupancy()
    {
        $aircraft = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 0,
                'altitude' => 0
            ]
        );
        $aircraft->occupiedStand()->sync([1 => ['latitude' => 51.47187222, 'longitude' => -5.22198972]]);

        $this->service->setOccupiedStands();
        $aircraft->refresh();
        $this->assertEquals(2, $aircraft->occupiedStand->first()->id);
    }

    public function testItRemovesOccupiedStandIfTheAircraftChangesLatitudeAndIsNoLongerOnStand()
    {
        $aircraft = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 53.65883639,
                'longitude' => -0.48601389,
                'groundspeed' => 0,
                'altitude' => 0
            ]
        );
        $aircraft->occupiedStand()->sync([1 => ['latitude' => 51.47187222, 'longitude' => -0.48601389]]);

        $this->service->setOccupiedStands();
        $aircraft->refresh();
        $this->assertNull($aircraft->occupiedStand->first());
    }

    public function testItRemovesOccupiedStandIfTheAircraftChangesLongitudeAndIsNoLongerOnStand()
    {
        $aircraft = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -5.22198972,
                'groundspeed' => 0,
                'altitude' => 0
            ]
        );
        $aircraft->occupiedStand()->sync([1 => ['latitude' => 51.47187222, 'longitude' => -0.48601389]]);

        $this->service->setOccupiedStands();
        $aircraft->refresh();
        $this->assertNull($aircraft->occupiedStand->first());
    }

    public function testItUsurpsAssignedStands()
    {
        Event::fake();
        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47187222,
                'longitude' => -0.48601389,
                'groundspeed' => 0,
                'altitude' => 0
            ]
        );

        $this->addStandAssignment('BAW123', 2);

        $this->service->setOccupiedStands();
        Event::assertDispatched(StandUnassignedEvent::class);
        $this->assertDatabaseMissing(
            'stand_assignments',
            ['callsign' => 'BAW123']
        );
    }

    public function testItReturnsClosestOccupiedStandIfMultipleInContention()
    {
        // Create an extra stand that's the closest
        $newStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST',
                'latitude' => 51.47437111,
                'longitude' => -0.48953611,
            ]
        );

        $aircraft = NetworkAircraftService::createOrUpdateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 51.47437111,
                'longitude' => -0.48953611,
                'groundspeed' => 0,
                'altitude' => 0
            ]
        );

        $this->service->setOccupiedStands();
        $aircraft->refresh();
        $this->assertCount(1, $aircraft->occupiedStand);
        $this->assertEquals($newStand->id, $aircraft->occupiedStand->first()->id);
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
