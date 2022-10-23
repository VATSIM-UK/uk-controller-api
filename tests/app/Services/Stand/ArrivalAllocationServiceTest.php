<?php

namespace App\Services\Stand;

use App\BaseFunctionalTestCase;
use App\Events\StandUnassignedEvent;
use App\Models\Stand\StandAssignment;
use App\Services\NetworkAircraftService;
use Illuminate\Support\Facades\Event;

class ArrivalAllocationServiceTest extends BaseFunctionalTestCase
{
    private readonly ArrivalAllocationService $service;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
        $this->service = $this->app->make(ArrivalAllocationService::class);
    }

    public function testItDeallocatesStandForDivertingAircraft()
    {
        $this->addStandAssignment('BMI221', 3);

        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BMI221',
            [
                'planned_aircraft' => 'B738',
                'planned_depairport' => 'EGKK',
                'planned_destairport' => 'EGLL',
                'groundspeed' => 150,
                // London
                'latitude' => 51.487202,
                'longitude' => -0.466667,
            ]
        );

        $this->service->allocateStandsAtArrivalAirfields();
        $this->assertNull(StandAssignment::find('BMI221'));
        Event::assertDispatched(fn(StandUnassignedEvent $event) => $event->getCallsign() === 'BMI221');
    }

    public function testItDoesntDeallocateStandIfAircraftNotDiverting()
    {
        $this->addStandAssignment('BMI221', 1);

        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BMI221',
            [
                'planned_aircraft' => 'B738',
                'planned_destairport' => 'EGLL',
                'groundspeed' => 150,
                // London
                'latitude' => 51.487202,
                'longitude' => -0.466667,
            ]
        );

        $this->service->allocateStandsAtArrivalAirfields();
        $this->assertEquals(1, StandAssignment::find('BMI221')->stand_id);
        Event::assertNotDispatched(fn(StandUnassignedEvent $event) => $event->getCallsign() === 'BMI221');
    }

    public function testItDoesntDeallocateStandIfForDepartureAirport()
    {
        $this->addStandAssignment('BMI221', 3);

        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BMI221',
            [
                'planned_aircraft' => 'B738',
                'planned_depairport' => 'EGBB',
                'planned_destairport' => 'EGLL',
                'groundspeed' => 150,
                // London
                'latitude' => 51.487202,
                'longitude' => -0.466667,
            ]
        );

        $this->service->allocateStandsAtArrivalAirfields();
        $this->assertEquals(3, StandAssignment::find('BMI221')->stand_id);
        Event::assertNotDispatched(fn(StandUnassignedEvent $event) => $event->getCallsign() === 'BMI221');
    }

    public function testItDoesntDeallocateStandIfNoStandToDeallocate()
    {
        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BMI221',
            [
                'planned_aircraft' => 'B738',
                'planned_destairport' => 'EGLL',
                'groundspeed' => 150,
                // London
                'latitude' => 51.487202,
                'longitude' => -0.466667,
            ]
        );

        $this->service->allocateStandsAtArrivalAirfields();
        Event::assertNotDispatched(fn(StandUnassignedEvent $event) => $event->getCallsign() === 'BMI221');
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
