<?php

namespace App\Services;

use App\Allocator\Stand\AirlineArrivalStandAllocator;
use App\Allocator\Stand\AirlineDestinationArrivalStandAllocator;
use App\Allocator\Stand\AirlineTerminalArrivalStandAllocator;
use App\Allocator\Stand\CargoArrivalStandAllocator;
use App\Allocator\Stand\DomesticInternationalStandAllocator;
use App\Allocator\Stand\SizeAppropriateArrivalStandAllocator;
use App\BaseFunctionalTestCase;
use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Exceptions\Stand\StandAlreadyAssignedException;
use App\Exceptions\Stand\StandNotFoundException;
use App\Models\Aircraft\Aircraft;
use App\Models\Dependency\Dependency;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
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
                'uri' => 'stand/dependency',
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

        $this->service->setOccupiedStand($aircraft);
        $aircraft->refresh();
        $this->assertEmpty($aircraft->occupiedStand);
    }

    public function testItDoesntOccupyStandsIfAircraftTooFast()
    {
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
        $this->expectsEvents(StandUnassignedEvent::class);
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
        $aircraft = NetworkDataService::firstOrCreateNetworkAircraft(
            'RYR787',
            [
                'latitude' => 54.65883639,
                'longitude' => -6.22198972,
                'groundspeed' => 0,
                'altitude' => 0
            ]
        );

        $this->assertEquals(2, $this->service->setOccupiedStand($aircraft)->id);
        $aircraft->refresh();
        $this->assertCount(1, $aircraft->occupiedStand);
        $this->assertEquals(2, $aircraft->occupiedStand->first()->id);
    }

    public function testItReturnsClosestOccupiedStandIfMultipleInContention()
    {
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
                AirlineDestinationArrivalStandAllocator::class,
                AirlineArrivalStandAllocator::class,
                AirlineTerminalArrivalStandAllocator::class,
                CargoArrivalStandAllocator::class,
                DomesticInternationalStandAllocator::class,
                SizeAppropriateArrivalStandAllocator::class,
            ],
            $this->service->getAllocatorPreference()
        );
    }

    public function testItAllocatesAStandFromAllocator()
    {
        $this->expectsEvents(StandAssignedEvent::class);
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

        $assignment = $this->service->allocateStandForAircraft($aircraft);
        $this->assertEquals(1, $assignment->stand_id);
        $this->assertEquals('BMI221', $assignment->callsign);
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
