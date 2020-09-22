<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Exceptions\Stand\StandAlreadyAssignedException;
use App\Exceptions\Stand\StandNotFoundException;
use App\Models\Stand\StandAssignment;
use App\Models\Vatsim\NetworkAircraft;

class StandServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var StandService
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(StandService::class);
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
                'BAW123' => 1,
                'BAW456' => 2
            ]
        );

        $this->assertEquals($expected, $this->service->getStandAssignments());
    }

    public function testItThrowsExceptionIfStandNotFound()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        $this->expectException(StandNotFoundException::class);
        $this->expectExceptionMessage('Stand with id 55 not found');
        $this->service->assignStandToAircraft('RYR7234', 55);
    }

    public function testItThrowsExceptionIfStandAlreadyAssigned()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        $this->expectException(StandAlreadyAssignedException::class);
        $this->expectExceptionMessage('Stand id 1 is already assigned to RYR923');
        $this->addStandAssignment('RYR923', 1);
        $this->service->assignStandToAircraft('RYR7234', 1);
    }

    public function testItAddsNewStandAssignment()
    {
        $this->expectsEvents(StandAssignedEvent::class);
        $this->service->assignStandToAircraft('RYR7234', 1);

        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'RYR7234',
                'stand_id' => 1,
            ]
        );
    }

    public function testItUpdatesExistingStandAssignment()
    {
        $this->expectsEvents(StandAssignedEvent::class);
        $this->addStandAssignment('RYR923', 1);
        $this->service->assignStandToAircraft('RYR7234', 2);

        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'RYR7234',
                'stand_id' => 2,
            ]
        );
    }

    public function testItAllowsAssignmentToSameStand()
    {
        $this->expectsEvents(StandAssignedEvent::class);
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
