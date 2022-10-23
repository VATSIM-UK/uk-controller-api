<?php

namespace App\Services\Stand;

use App\Allocator\Stand\AirlineArrivalStandAllocator;
use App\Allocator\Stand\AirlineCallsignSlugArrivalStandAllocator;
use App\Allocator\Stand\AirlineDestinationArrivalStandAllocator;
use App\Allocator\Stand\AirlineTerminalArrivalStandAllocator;
use App\Allocator\Stand\ArrivalStandAllocatorInterface;
use App\Allocator\Stand\CallsignFlightplanReservedArrivalStandAllocator;
use App\Allocator\Stand\CargoAirlineFallbackStandAllocator;
use App\Allocator\Stand\CargoFlightArrivalStandAllocator;
use App\Allocator\Stand\CargoFlightPreferredArrivalStandAllocator;
use App\Allocator\Stand\CidReservedArrivalStandAllocator;
use App\Allocator\Stand\DomesticInternationalStandAllocator;
use App\Allocator\Stand\FallbackArrivalStandAllocator;
use App\BaseFunctionalTestCase;
use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Models\Aircraft\Aircraft;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandReservation;
use App\Services\NetworkAircraftService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class ArrivalAllocationServiceTest extends BaseFunctionalTestCase
{
    private readonly ArrivalAllocationService $service;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
        $this->service = $this->app->make(ArrivalAllocationService::class);
        DB::table('network_aircraft')->delete();
    }

    public function testItDeallocatesStandForDivertingAircraft()
    {
        $this->addStandAssignment('BMI221', 3);

        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BMI221',
            [
                'planned_aircraft' => 'B738',
                'planned_depairport' => 'EGKK',
                'planned_destairport' => 'EGXY',
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

    public function testItAllocatesANewStandForDivertingAircraft()
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
        $this->assertNotNull(StandAssignment::find('BMI221'));
        $this->assertTrue(in_array(StandAssignment::find('BMI221')->stand_id, [1, 2]));
        Event::assertDispatched(fn(StandUnassignedEvent $event) => $event->getCallsign() === 'BMI221');
        Event::assertDispatched(fn(StandAssignedEvent $event) => $event->getStandAssignment()->callsign === 'BMI221');
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

    public function testItHasAllocatorPreference()
    {
        $this->assertEquals(
            [
                CidReservedArrivalStandAllocator::class,
                CallsignFlightplanReservedArrivalStandAllocator::class,
                CargoFlightPreferredArrivalStandAllocator::class,
                CargoFlightArrivalStandAllocator::class,
                AirlineCallsignSlugArrivalStandAllocator::class,
                AirlineDestinationArrivalStandAllocator::class,
                AirlineArrivalStandAllocator::class,
                AirlineTerminalArrivalStandAllocator::class,
                CargoAirlineFallbackStandAllocator::class,
                DomesticInternationalStandAllocator::class,
                FallbackArrivalStandAllocator::class,
            ],
            array_map(
                fn(ArrivalStandAllocatorInterface $allocator) => get_class($allocator),
                $this->service->getAllocators()
            )
        );
    }

    public function testItAllocatesAStandFromAllocator()
    {
        $this->expectsEvents(StandAssignedEvent::class);
        StandReservation::create(
            [
                'callsign' => 'BMI221',
                'stand_id' => 1,
                'start' => Carbon::now()->subMinute(),
                'end' => Carbon::now()->addMinute(),
                'destination' => 'EGLL',
                'origin' => 'EGSS',
            ]
        );

        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BMI221',
            [
                'planned_aircraft' => 'B738',
                'planned_destairport' => 'EGLL',
                'planned_depairport' => 'EGSS',
                'groundspeed' => 150,
                // London
                'latitude' => 51.487202,
                'longitude' => -0.466667,
            ]
        );

        $this->service->allocateStandsAtArrivalAirfields();
        $this->assertEquals(1, StandAssignment::find('BMI221')->stand_id);
    }

    public function testItDoesntAllocateStandIfPerformingCircuits()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BMI221',
            [
                'planned_aircraft' => 'B738',
                'planned_destairport' => 'EGLL',
                'planned_depairport' => 'EGLL',
                'groundspeed' => 150,
                // London
                'latitude' => 51.487202,
                'longitude' => -0.466667,
            ]
        );

        $this->service->allocateStandsAtArrivalAirfields();
        $this->assertFalse(StandAssignment::where('callsign', 'BMI221')->exists());
    }

    public function testItDoesntPerformAllocationIfStandTooFarFromAirfield()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BMI221',
            [
                'planned_aircraft' => 'B738',
                'planned_destairport' => 'EGLL',
                'groundspeed' => 100,
                // Lambourne
                'latitude' => 51.646099,
                'longitude' => 0.151667,
            ]
        );

        $this->service->allocateStandsAtArrivalAirfields();
        $this->assertFalse(StandAssignment::where('callsign', 'BMI221')->exists());
    }

    public function testItDoesntPerformAllocationIfAircraftHasNoGroundspeed()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BMI221',
            [
                'planned_aircraft' => 'B738',
                'planned_destairport' => 'EGLL',
                'groundspeed' => 0,
                // Lambourne
                'latitude' => 51.646099,
                'longitude' => 0.151667,
            ]
        );

        $this->service->allocateStandsAtArrivalAirfields();
        $this->assertFalse(StandAssignment::where('callsign', 'BMI221')->exists());
    }

    public function testItDoesntPerformAllocationIfNoStandAllocated()
    {
        // Delete all the stands so there's nothing to allocate
        Stand::all()->each(function (Stand $stand) {
            $stand->delete();
        });

        $this->doesntExpectEvents(StandAssignedEvent::class);
        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BMI221',
            [
                'planned_aircraft' => 'B738',
                'planned_destairport' => 'EGLL',
                'groundspeed' => 150,
                // Lambourne
                'latitude' => 51.646099,
                'longitude' => 0.151667,
            ]
        );

        $this->service->allocateStandsAtArrivalAirfields();
        $this->assertFalse(StandAssignment::where('callsign', 'BMI221')->exists());
    }

    public function testItDoesntPerformAllocationIfStandAlreadyAssigned()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BMI221',
            [
                'planned_aircraft' => 'B738',
                'planned_destairport' => 'EGLL',
                'groundspeed' => 150,
                // Lambourne
                'latitude' => 51.646099,
                'longitude' => 0.151667,
            ]
        );
        StandAssignment::create(
            [
                'callsign' => 'BMI221',
                'stand_id' => 1,
            ]
        );

        $this->service->allocateStandsAtArrivalAirfields();
        $this->assertTrue(StandAssignment::where('callsign', 'BMI221')->where('stand_id', 1)->exists());
    }

    public function testItDoesntReturnAllocationIfAirfieldNotFound()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BMI221',
            [
                'planned_aircraft' => 'B738',
                'planned_destairport' => 'EGXX',
                'groundspeed' => 150,
                // Lambourne
                'latitude' => 51.646099,
                'longitude' => 0.151667,
            ]
        );

        $this->service->allocateStandsAtArrivalAirfields();
        $this->assertFalse(StandAssignment::where('callsign', 'BMI221')->exists());
    }

    public function testItDoesntPerformAllocationIfUnknownAircraftType()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BMI221',
            [
                'planned_aircraft' => 'B736',
                'planned_destairport' => 'EGLL',
                'groundspeed' => 150,
                // Lambourne
                'latitude' => 51.646099,
                'longitude' => 0.151667,
            ]
        );

        $this->service->allocateStandsAtArrivalAirfields();
        $this->assertFalse(StandAssignment::where('callsign', 'BMI221')->exists());
    }

    public function testItDoesntPerformAllocationIfAircraftTypeNotStandAssignable()
    {
        Aircraft::where('code', 'B738')->update(['allocate_stands' => false]);

        $this->doesntExpectEvents(StandAssignedEvent::class);
        NetworkAircraftService::createOrUpdateNetworkAircraft(
            'BMI221',
            [
                'planned_aircraft' => 'B738',
                'planned_destairport' => 'EGLL',
                'groundspeed' => 150,
                // Lambourne
                'latitude' => 51.646099,
                'longitude' => 0.151667,
            ]
        );

        $this->service->allocateStandsAtArrivalAirfields();
        $this->assertFalse(StandAssignment::where('callsign', 'BMI221')->exists());
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
