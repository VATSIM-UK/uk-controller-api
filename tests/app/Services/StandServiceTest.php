<?php

namespace App\Services;

use App\Allocator\Stand\AirlineArrivalStandAllocator;
use App\Allocator\Stand\AirlineDestinationArrivalStandAllocator;
use App\Allocator\Stand\AirlineTerminalArrivalStandAllocator;
use App\Allocator\Stand\CargoArrivalStandAllocator;
use App\Allocator\Stand\DomesticInternationalStandAllocator;
use App\Allocator\Stand\FallbackArrivalStandAllocator;
use App\Allocator\Stand\ReservedArrivalStandAllocator;
use App\Allocator\Stand\GeneralUseArrivalStandAllocator;
use App\BaseFunctionalTestCase;
use App\Events\StandAssignedEvent;
use App\Events\StandOccupiedEvent;
use App\Events\StandUnassignedEvent;
use App\Events\StandVacatedEvent;
use App\Exceptions\Stand\StandAlreadyAssignedException;
use App\Exceptions\Stand\StandNotFoundException;
use App\Models\Aircraft\Aircraft;
use App\Models\Dependency\Dependency;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandReservation;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;

class StandServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var StandService
     */
    private $service;

    /**
     * @var Dependency
     */
    private $dependency;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(StandService::class);
        $this->dependency = Dependency::create(
            [
                'key' => StandService::STAND_DEPENDENCY_KEY,
                'action' => 'foo',
                'local_file' => 'stands.json'
            ]
        );
        $this->dependency->updated_at = null;
        $this->dependency->save();
    }

    public function testItReturnsStandDependency()
    {
        $expected = collect(
            [
                'EGLL' => collect(
                    [
                        [
                            'id' => 1,
                            'identifier' => '1L',
                        ],
                        [
                            'id' => 2,
                            'identifier' => '251',
                        ],
                    ]
                ),
                'EGBB' => collect(
                    [
                        [
                            'id' => 3,
                            'identifier' => '32',
                        ]
                    ]
                ),
            ]
        );

        $this->assertEquals($expected, $this->service->getStandsDependency());
    }

    public function testItReturnsAllStandAssignments()
    {
        StandAssignment::insert(
            [
                [
                    'callsign' => 'BAW123',
                    'stand_id' => 1,
                ],
                [
                    'callsign' => 'BAW456',
                    'stand_id' => 2,
                ],
            ]
        );

        $expected = collect(
            [
                [
                    'callsign' => 'BAW123',
                    'stand_id' => 1,
                ],
                [
                    'callsign' => 'BAW456',
                    'stand_id' => 2,
                ],
            ]
        );

        $this->assertEquals($expected, $this->service->getStandAssignments());
    }

    public function testAssignStandToAircraftThrowsExceptionIfStandNotFound()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        $this->expectException(StandNotFoundException::class);
        $this->expectExceptionMessage('Stand with id 55 not found');
        $this->service->assignStandToAircraft('RYR7234', 55);
    }

    public function testAssignStandToAircraftAddsNewStandAssignment()
    {
        $this->expectsEvents(StandAssignedEvent::class);
        $this->doesntExpectEvents(StandUnassignedEvent::class);
        $this->service->assignStandToAircraft('RYR7234', 1);

        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'RYR7234',
                'stand_id' => 1,
            ]
        );
    }

    public function testAssignStandToAircraftUpdatesExistingStandAssignment()
    {
        $this->expectsEvents(StandAssignedEvent::class);
        $this->doesntExpectEvents(StandUnassignedEvent::class);
        $this->addStandAssignment('RYR7234', 1);
        $this->service->assignStandToAircraft('RYR7234', 2);

        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'RYR7234',
                'stand_id' => 2,
            ]
        );
    }

    public function testAssignStandToAircraftUnassignsExistingAssignment()
    {
        $this->addStandAssignment('BAW123', 1);
        $this->expectsEvents(StandAssignedEvent::class);
        $this->expectsEvents(StandUnassignedEvent::class);
        $this->service->assignStandToAircraft('RYR7234', 1);

        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'RYR7234',
                'stand_id' => 1,
            ]
        );
        $this->assertDatabaseMissing(
            'stand_assignments',
            [
                'callsign' => 'BAW123'
            ]
        );
    }

    public function testAssignStandToAircraftUnassignsExistingAssignmentToPairedStand()
    {
        Stand::find(1)->pairedStands()->sync([2]);
        $this->addStandAssignment('BAW123', 2);
        $this->expectsEvents(StandAssignedEvent::class);
        $this->expectsEvents(StandUnassignedEvent::class);

        $this->service->assignStandToAircraft('RYR7234', 1);

        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'RYR7234',
                'stand_id' => 1,
            ]
        );
        $this->assertDatabaseMissing(
            'stand_assignments',
            [
                'callsign' => 'BAW123'
            ]
        );
    }

    public function testItDoesntTriggerUnassignmentIfMovingWithinPair()
    {
        Stand::find(1)->pairedStands()->sync([2]);
        $this->addStandAssignment('BAW123', 2);
        $this->expectsEvents(StandAssignedEvent::class);
        $this->doesntExpectEvents(StandUnassignedEvent::class);

        $this->service->assignStandToAircraft('BAW123', 1);

        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );
    }

    public function testAssignStandToAircraftAllowsAssignmentToSameStand()
    {
        $this->expectsEvents(StandAssignedEvent::class);
        $this->doesntExpectEvents(StandUnassignedEvent::class);
        $this->addStandAssignment('RYR7234', 1);
        $this->service->assignStandToAircraft('RYR7234', 1);

        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'RYR7234',
                'stand_id' => 1,
            ]
        );
    }

    public function testAssignAircraftToStandThrowsExceptionIfStandNotFound()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        $this->expectException(StandNotFoundException::class);
        $this->expectExceptionMessage('Stand with id 55 not found');
        $this->service->assignAircraftToStand('RYR7234', 55);
    }

    public function testAssignAircraftToStandThrowsExceptionIfAlreadyAssigned()
    {
        $this->addStandAssignment('BAW123', 1);
        $this->doesntExpectEvents(StandAssignedEvent::class);
        $this->expectException(StandAlreadyAssignedException::class);
        $this->expectExceptionMessage('Stand id 1 is already assigned to BAW123');
        $this->service->assignAircraftToStand('RYR7234', 1);

        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );
        $this->assertDatabaseMissing(
            'stand_assignments',
            [
                'callsign' => 'RYR7234'
            ]
        );
    }

    public function testAssignAircraftToStandAddsNewStandAssignment()
    {
        $this->expectsEvents(StandAssignedEvent::class);
        $this->service->assignAircraftToStand('RYR7234', 1);

        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'RYR7234',
                'stand_id' => 1,
            ]
        );
    }

    public function testAssignAircraftToStandUpdatesExistingStandAssignment()
    {
        $this->expectsEvents(StandAssignedEvent::class);
        $this->doesntExpectEvents(StandUnassignedEvent::class);
        $this->addStandAssignment('RYR7234', 1);
        $this->service->assignAircraftToStand('RYR7234', 2);

        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'RYR7234',
                'stand_id' => 2,
            ]
        );
    }

    public function testAssignAircraftToStandAllowsAssignmentToSameStand()
    {
        $this->expectsEvents(StandAssignedEvent::class);
        $this->doesntExpectEvents(StandUnassignedEvent::class);
        $this->addStandAssignment('RYR7234', 1);
        $this->service->assignAircraftToStand('RYR7234', 1);

        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'RYR7234',
                'stand_id' => 1,
            ]
        );
    }

    public function testItDeletesStandAssignments()
    {
        $this->expectsEvents(StandUnassignedEvent::class);
        $this->addStandAssignment('RYR7234', 1);
        $this->service->deleteStandAssignmentByCallsign('RYR7234');

        $this->assertDatabaseMissing(
            'stand_assignments',
            [
                'callsign' => 'RYR7234',
            ]
        );
    }

    public function testItDoesntTriggerEventIfNoAssignmentDelete()
    {
        $this->doesntExpectEvents(StandUnassignedEvent::class);
        $this->service->deleteStandAssignmentByCallsign('RYR7234');

        $this->assertDatabaseMissing(
            'stand_assignments',
            [
                'callsign' => 'RYR7234',
            ]
        );
    }

    public function testItGetsDepartureStandAssignmentForAircraft()
    {
        $this->addStandAssignment('BAW123', 1);
        NetworkAircraft::where('callsign', 'BAW123')->update(['planned_depairport' => 'EGLL']);

        $this->assertEquals(
            StandAssignment::find('BAW123'),
            $this->service->getDepartureStandAssignmentForAircraft(NetworkAircraft::find('BAW123'))
        );
    }

    public function testItDoesntGetDepartureStandIfAssignmentNotForDepartureAirport()
    {
        $this->addStandAssignment('BAW123', 1);
        NetworkAircraft::where('callsign', 'BAW123')->update(['planned_depairport' => 'EGBB']);
        $this->assertNull($this->service->getDepartureStandAssignmentForAircraft(NetworkAircraft::find('BAW123')));
    }

    public function testItDoesntGetDepartureStandIfNoAssignment()
    {
        $this->assertNull($this->service->getDepartureStandAssignmentForAircraft(NetworkAircraft::find('BAW123')));
    }

    public function testItDeletesAStand()
    {
        $this->service->deleteStand('EGLL', '1L');
        $this->assertDatabaseMissing(
            'stands',
            [
                'airfield_id' => 1,
                'identifier' => '1L'
            ]
        );
    }

    public function testDeletingAStandUpdatesStandDependency()
    {
        $this->assertNull($this->dependency->updated_at);
        $this->service->deleteStand('EGLL', '1L');
        $this->dependency->refresh();
        $this->assertNotNull($this->dependency->updated_at);
    }

    public function testItDoesNotUpdateDependencyOnDeletingNonExistentStand()
    {
        $this->assertNull($this->dependency->updated_at);
        $this->service->deleteStand('EGLL', 'ABCD');
        $this->dependency->refresh();
        $this->assertNull($this->dependency->updated_at);
    }

    public function testItChangesAStandIdentifier()
    {
        $this->service->changeStandIdentifier('EGLL', '1L', '1R');
        $this->assertDatabaseMissing(
            'stands',
            [
                'airfield_id' => 1,
                'identifier' => '1L'
            ]
        );
        $this->assertDatabaseHas(
            'stands',
            [
                'airfield_id' => 1,
                'identifier' => '1R'
            ]
        );
    }

    public function testChangingAStandIdentifierUpdatesStandDependency()
    {
        $this->assertNull($this->dependency->updated_at);
        $this->service->changeStandIdentifier('EGLL', '1L', '1R');
        $this->dependency->refresh();
        $this->assertNotNull($this->dependency->updated_at);
    }

    public function testItDoesNotUpdateDependencyOnChangingIdentifierOfNonExistentStand()
    {
        $this->assertNull($this->dependency->updated_at);
        $this->service->changeStandIdentifier('EGLL', 'ABCD', '1R');
        $this->dependency->refresh();
        $this->assertNull($this->dependency->updated_at);
    }

    public function testItDoesntOccupyStandsIfAircraftTooHigh()
    {
        $this->expectsEvents(StandVacatedEvent::class);
        $this->doesntExpectEvents(StandOccupiedEvent::class);
        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 54.65883639,
                'longitude' => -6.22198972,
                'groundspeed' => 10,
                'altitude' => 751
            ]
        );
        $aircraft->occupiedStand()->sync([1]);

        $this->assertNull($this->service->setOccupiedStand($aircraft));
        $aircraft->refresh();
        $this->assertEmpty($aircraft->occupiedStand);
    }

    public function testItRemovesOccupiedStandIfAircraftTooHigh()
    {
        $this->expectsEvents(StandVacatedEvent::class);
        $this->doesntExpectEvents(StandOccupiedEvent::class);
        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 54.65883639,
                'longitude' => -6.22198972,
                'groundspeed' => 0,
                'altitude' => 751
            ]
        );
        $aircraft->occupiedStand()->sync([1]);

        $this->service->setOccupiedStand($aircraft);
        $aircraft->refresh();
        $this->assertEmpty($aircraft->occupiedStand);
    }

    public function testItDoesntOccupyStandsIfAircraftTooFast()
    {
        $this->expectsEvents(StandVacatedEvent::class);
        $this->doesntExpectEvents(StandOccupiedEvent::class);
        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 54.65883639,
                'longitude' => -6.22198972,
                'groundspeed' => 6,
                'altitude' => 0
            ]
        );
        $aircraft->occupiedStand()->sync([1]);

        $this->assertNull($this->service->setOccupiedStand($aircraft));
        $aircraft->refresh();
        $this->assertEmpty($aircraft->occupiedStand);
    }

    public function testItRemovesOccupiedStandIfAircraftTooFast()
    {
        $this->expectsEvents(StandVacatedEvent::class);
        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 54.65883639,
                'longitude' => -6.22198972,
                'groundspeed' => 11,
                'altitude' => 0
            ]
        );

        $aircraft->occupiedStand()->sync([1]);
        $this->service->setOccupiedStand($aircraft);
        $aircraft->refresh();
        $this->assertEmpty($aircraft->occupiedStand);
    }

    public function testItReturnsCurrentStandIfStillOccupied()
    {
        $this->doesntExpectEvents(StandOccupiedEvent::class);
        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 54.65883639,
                'longitude' => -6.22198972,
                'groundspeed' => 0,
                'altitude' => 0
            ]
        );
        $aircraft->occupiedStand()->sync([2]);
        $this->assertEquals(2, $this->service->setOccupiedStand($aircraft)->id);
        $aircraft->refresh();
        $this->assertEquals(2, $aircraft->occupiedStand->first()->id);
    }

    public function testItDoesntReturnCurrentStandIfNoLongerOccupied()
    {
        $this->expectsEvents([StandOccupiedEvent::class]);
        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 54.65883639,
                'longitude' => -6.22198972,
                'groundspeed' => 0,
                'altitude' => 0
            ]
        );
        $aircraft->occupiedStand()->sync([1]);

        $this->assertEquals(2, $this->service->setOccupiedStand($aircraft)->id);
        $aircraft->refresh();
        $this->assertEquals(2, $aircraft->occupiedStand->first()->id);
    }

    public function testItUsurpsAssignedStands()
    {
        $this->expectsEvents([StandOccupiedEvent::class, StandUnassignedEvent::class]);
        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 54.65883639,
                'longitude' => -6.22198972,
                'groundspeed' => 0,
                'altitude' => 0
            ]
        );

        $assignment = $this->addStandAssignment('BAW123', 2);

        $this->service->setOccupiedStand($aircraft);
        $this->assertDeleted($assignment);
    }

    public function testItReturnsOccupiedStandIfStandIsOccupied()
    {
        $this->doesntExpectEvents(StandOccupiedEvent::class);
        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 54.65883639,
                'longitude' => -6.22198972,
                'groundspeed' => 0,
                'altitude' => 0
            ]
        );
        $aircraft->occupiedStand()->sync([2]);

        $this->assertEquals(2, $this->service->setOccupiedStand($aircraft)->id);
        $aircraft->refresh();
        $this->assertCount(1, $aircraft->occupiedStand);
        $this->assertEquals(2, $aircraft->occupiedStand->first()->id);
    }

    public function testItReturnsClosestOccupiedStandIfMultipleInContention()
    {
        $this->expectsEvents(StandOccupiedEvent::class);
        // Create an extra stand that's the closest
        $newStand = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST',
                'latitude' => 54.658828,
                'longitude' =>  -6.222070,
            ]
        );

        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 54.658828,
                'longitude' => -6.222070,
                'groundspeed' => 0,
                'altitude' => 0
            ]
        );

        $this->assertEquals($newStand->id, $this->service->setOccupiedStand($aircraft)->id);
        $aircraft->refresh();
        $this->assertCount(1, $aircraft->occupiedStand);
        $this->assertEquals($newStand->id, $aircraft->occupiedStand->first()->id);
    }

    public function testItHasAllocatorPreference()
    {
        $this->assertEquals(
            [
                ReservedArrivalStandAllocator::class,
                AirlineDestinationArrivalStandAllocator::class,
                AirlineArrivalStandAllocator::class,
                AirlineTerminalArrivalStandAllocator::class,
                CargoArrivalStandAllocator::class,
                DomesticInternationalStandAllocator::class,
                FallbackArrivalStandAllocator::class,
            ],
            $this->service->getAllocatorPreference()
        );
    }

    public function testItDeallocatesStandForDivertingAircraft()
    {
        $this->addStandAssignment('BMI221', 3);
        $this->expectsEvents(StandUnassignedEvent::class);

        $aircraft = NetworkDataService::createOrUpdateNetworkAircraft(
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

        $this->service->removeAllocationIfDestinationChanged($aircraft);
        $this->assertNull(StandAssignment::find('BMI221'));
    }

    public function testItDoesntDeallocateStandIfAircraftNotDiverting()
    {
        $this->addStandAssignment('BMI221', 1);
        $this->doesntExpectEvents(StandUnassignedEvent::class);

        $aircraft = NetworkDataService::createOrUpdateNetworkAircraft(
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

        $this->service->removeAllocationIfDestinationChanged($aircraft);
        $this->assertEquals(1, StandAssignment::find('BMI221')->stand_id);
    }

    public function testItDoesntDeallocateStandIfForDepartureAirport()
    {
        $this->addStandAssignment('BMI221', 3);
        $this->doesntExpectEvents(StandUnassignedEvent::class);

        $aircraft = NetworkDataService::createOrUpdateNetworkAircraft(
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

        $this->service->removeAllocationIfDestinationChanged($aircraft);
        $this->assertEquals(3, StandAssignment::find('BMI221')->stand_id);
    }

    public function testItDoesntDeallocateStandIfNoStandToDeallocate()
    {
        $this->doesntExpectEvents(StandUnassignedEvent::class);
        $aircraft = NetworkDataService::createOrUpdateNetworkAircraft(
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

        $this->service->removeAllocationIfDestinationChanged($aircraft);
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
            ]
        );

        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
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

        $assignment = $this->service->allocateStandForAircraft($aircraft);
        $this->assertEquals(1, $assignment->stand_id);
        $this->assertEquals('BMI221', $assignment->callsign);
    }

    public function testItDoesntAllocateStandIfPerformingCircuits()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
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

        $this->assertNull($this->service->allocateStandForAircraft($aircraft));
        $this->assertFalse(StandAssignment::where('callsign', 'BMI221')->exists());
    }

    public function testItDoesntPerformAllocationIfStandTooFarFromAirfield()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
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

        $this->assertNull($this->service->allocateStandForAircraft($aircraft));
        $this->assertFalse(StandAssignment::where('callsign', 'BMI221')->exists());
    }

    public function testItDoesntPerformAllocationIfAircraftHasNoGroundspeed()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
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

        $this->assertNull($this->service->allocateStandForAircraft($aircraft));
        $this->assertFalse(StandAssignment::where('callsign', 'BMI221')->exists());
    }

    public function testItDoesntPerformAllocationIfNoStandAllocated()
    {
        // Delete all the stands so there's nothing to allocate
        Stand::all()->each(function (Stand $stand) {
            $stand->delete();
        });

        $this->doesntExpectEvents(StandAssignedEvent::class);
        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
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

        $this->assertNull($this->service->allocateStandForAircraft($aircraft));
        $this->assertFalse(StandAssignment::where('callsign', 'BMI221')->exists());
    }

    public function testItDoesntPerformAllocationIfStandAlreadyAssigned()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
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
                'stand_id' => 1
            ]
        );

        $this->assertNull($this->service->allocateStandForAircraft($aircraft));
        $this->assertTrue(StandAssignment::where('callsign', 'BMI221')->where('stand_id', 1)->exists());
    }

    public function testItDoesntReturnAllocationIfAirfieldNotFound()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
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

        $this->assertNull($this->service->allocateStandForAircraft($aircraft));
        $this->assertFalse(StandAssignment::where('callsign', 'BMI221')->exists());
    }

    public function testItDoesntPerformAllocationIfUnknownAircraftType()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
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

        $this->assertNull($this->service->allocateStandForAircraft($aircraft));
        $this->assertFalse(StandAssignment::where('callsign', 'BMI221')->exists());
    }

    public function testItDoesntPerformAllocationIfAircraftTypeNotStandAssignable()
    {
        Aircraft::where('code', 'B738')->update(['allocate_stands' => false]);

        $this->doesntExpectEvents(StandAssignedEvent::class);
        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
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

        $this->assertNull($this->service->allocateStandForAircraft($aircraft));
        $this->assertFalse(StandAssignment::where('callsign', 'BMI221')->exists());
    }

    public function testItReturnsFreeStandsAtAirfields()
    {
        $stand1 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST1',
                'latitude' => 54.658828,
                'longitude' =>  -6.222070,
            ]
        );

        Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST2',
                'latitude' => 54.658828,
                'longitude' =>  -6.222070,
            ]
        );

        $this->addStandAssignment('BAW959', $stand1->id);

        $this->assertEquals(
            [
                '1L',
                '251',
                'TEST2',
            ],
            $this->service->getAvailableStandsForAirfield('EGLL')->toArray()
        );
    }

    public function testItReturnsAnAircraftsStandAssignment()
    {
        $this->addStandAssignment('BAW959', 1);
        $this->assertEquals(1, $this->service->getAssignedStandForAircraft('BAW959')->id);
    }

    public function testItReturnsNullIfNoStandAssignmentForAircraft()
    {
        $this->assertNull($this->service->getAssignedStandForAircraft('BAW959'));
    }

    public function testItReturnsStandStatuses()
    {
        Carbon::setTestNow(Carbon::now());

        // Clear out all the stands so its easier to follow the test data.
        Stand::all()->each(function (Stand $stand) {
            $stand->delete();
        });

        // Stand 1 is free but has a reservation starting in a few hours, it also has an airline with some destinations
        $stand1 = Stand::create(
            [
                'airfield_id' => 1,
                'type_id' => 3,
                'identifier' => 'TEST1',
                'latitude' => 54.658828,
                'longitude' =>  -6.222070,
            ]
        );
        $this->addStandReservation('FUTURE-RESERVATION', $stand1->id, false);
        $stand1->airlines()->attach([1 => ['destination' => 'EDDM']]);
        $stand1->airlines()->attach([1 => ['destination' => 'EDDF']]);

        // Stand 2 is assigned, it has a max aircraft type
        $stand2 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST2',
                'latitude' => 54.658828,
                'longitude' =>  -6.222070,
                'max_aircraft_id' => 1,
            ]
        );
        $this->addStandAssignment('ASSIGNMENT', $stand2->id);

        // Stand 3 is reserved
        $stand3 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST3',
                'latitude' => 54.658828,
                'longitude' =>  -6.222070,
            ]
        );
        $this->addStandReservation('RESERVATION', $stand3->id, true);

        // Stand 4 is occupied
        $stand4 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST4',
                'latitude' => 54.658828,
                'longitude' =>  -6.222070,
            ]
        );
        $occupier = NetworkDataService::firstOrCreateNetworkAircraft('OCCUPIED');
        $occupier->occupiedStand()->sync($stand4);

        // Stand 5 is paired with stand 2 which is assigned
        $stand5 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST5',
                'latitude' => 54.658828,
                'longitude' =>  -6.222070,
            ]
        );
        $stand2->pairedStands()->sync($stand5);
        $stand5->pairedStands()->sync($stand2);

        // Stand 6 is paired with stand 3 which is reserved
        $stand6 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST6',
                'latitude' => 54.658828,
                'longitude' =>  -6.222070,
            ]
        );
        $stand3->pairedStands()->sync([$stand6->id]);
        $stand6->pairedStands()->sync([$stand3->id]);

        // Stand 7 is paired with stand 4 which is occupied
        $stand7 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST7',
                'latitude' => 54.658828,
                'longitude' =>  -6.222070,
            ]
        );
        $stand4->pairedStands()->sync([$stand7->id]);
        $stand7->pairedStands()->sync([$stand4->id]);

        // Stand 8 is paired with stand 1 which is free
        $stand8 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST8',
                'latitude' => 54.658828,
                'longitude' =>  -6.222070,
            ]
        );
        $stand1->pairedStands()->sync([$stand8->id]);
        $stand8->pairedStands()->sync([$stand1->id]);

        // Stand 9 is reserved in half an hour
        $stand9 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST9',
                'latitude' => 54.658828,
                'longitude' =>  -6.222070,
            ]
        );
        StandReservation::create(
            [
                'callsign' => null,
                'stand_id' => $stand9->id,
                'start' => Carbon::now()->addMinutes(59)->startOfSecond(),
                'end' => Carbon::now()->addHours(2),
            ]
        );

        $this->assertEquals(
            [
                [
                    'identifier' => 'TEST1',
                    'type' => 'CARGO',
                    'status' => 'available',
                    'airlines' => [
                        'BAW' => ['EDDM', 'EDDF']
                    ],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
                [
                    'identifier' => 'TEST2',
                    'type' => null,
                    'status' => 'assigned',
                    'callsign' => 'ASSIGNMENT',
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => 'B738',
                ],
                [
                    'identifier' => 'TEST3',
                    'type' => null,
                    'status' => 'reserved',
                    'callsign' => 'RESERVATION',
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
                [
                    'identifier' => 'TEST4',
                    'type' => null,
                    'status' => 'occupied',
                    'callsign' => 'OCCUPIED',
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
                [
                    'identifier' => 'TEST5',
                    'type' => null,
                    'status' => 'unavailable',
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
                [
                    'identifier' => 'TEST6',
                    'type' => null,
                    'status' => 'unavailable',
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
                [
                    'identifier' => 'TEST7',
                    'type' => null,
                    'status' => 'unavailable',
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
                [
                    'identifier' => 'TEST8',
                    'type' => null,
                    'status' => 'available',
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
                [
                    'identifier' => 'TEST9',
                    'type' => null,
                    'status' => 'reserved_soon',
                    'callsign' => null,
                    'reserved_at' => Carbon::now()->addMinutes(59)->startOfSecond(),
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
            ],
            $this->service->getAirfieldStandStatus('EGLL')
        );
    }

    public function testItReturnsAircraftWhoAreEligibleForArrivalStandAllocation()
    {
        $this->assertEquals(
            collect(
                [
                    NetworkAircraft::find('BAW123'),
                    NetworkAircraft::find('BAW456'),
                    NetworkAircraft::find('BAW789')
                ]
            ),
            $this->service->getAircraftEligibleForArrivalStandAllocation()->toBase()
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

    private function addStandReservation(string $callsign, int $standId, bool $active): StandReservation
    {
        NetworkDataService::firstOrCreateNetworkAircraft($callsign);
        return StandReservation::create(
            [
                'callsign' => $callsign,
                'stand_id' => $standId,
                'start' => $active ? Carbon::now() : Carbon::now()->addHours(2),
                'end' => Carbon::now()->addHours(2)->addMinutes(10),
            ]
        );
    }
}
