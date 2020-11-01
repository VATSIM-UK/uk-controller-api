<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Exceptions\Stand\StandAlreadyAssignedException;
use App\Exceptions\Stand\StandNotFoundException;
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
        $this->service->deleteStandAssignment('RYR7234');

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
        $this->service->deleteStandAssignment('RYR7234');

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
