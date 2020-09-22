<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Events\StandAssignedEvent;
use App\Events\StandUnassignedEvent;
use App\Models\Stand\StandAssignment;
use App\Services\NetworkDataService;

class StandControllerTest extends BaseApiTestCase
{
    public function testItReturnsStandDependency()
    {
        $expected = [
            [
                'id' => 1,
                'airfield_icao' => 'EGLL',
                'identifier' => '1L',
            ],
            [
                'id' => 2,
                'airfield_icao' => 'EGLL',
                'identifier' => '251',
            ],
            [
                'id' => 3,
                'airfield_icao' => 'EGBB',
                'identifier' => '32',
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'stand/dependency')
            ->assertJson($expected)
            ->assertStatus(200);
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

        $expected = [
            'BAW123' => 1,
            'BAW456' => 2
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'stand/assignment')
            ->assertJson($expected)
            ->assertStatus(200);
    }

    /**
     * @dataProvider badAssignmentDataProvider
     */
    public function testItReturnsInvalidRequestOnBadStandAssignmentData(array $data)
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'stand/assignment', $data)
            ->assertStatus(400);
    }

    public function badAssignmentDataProvider(): array
    {
        return [
            [[
                'callsign' => 'asdfdsdfdsfdsfdsfdsfsdfsd',
                'stand_id' => 1
            ]], // Invalid callsign
            [[
                'callsign' => null,
                'stand_id' => 1
            ]], // Callsign null
            [[
                'stand_id' => 1
            ]], // Callsign missing
            [[
                'callsign' => 'BAW123',
                'stand_id' => 'asdas'
            ]], // Invalid stand id
            [[
                'callsign' => 'BAW123',
            ]], // Stand id missing
            [[
                'callsign' => 'BAW123',
                'stand_id' => null
            ]],  // Stand id null
        ];
    }

    public function testItDoesStandAssignment()
    {
        $this->expectsEvents(StandAssignedEvent::class);
        $data = [
            'callsign' => 'BAW123',
            'stand_id' => 1
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'stand/assignment', $data)
            ->assertStatus(201);

        $this->assertDatabaseHas(
            'stand_assignments',
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );
    }

    public function testItReturnsNotFoundOnAssignmentIfStandDoesNotExist()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        $data = [
            'callsign' => 'BAW123',
            'stand_id' => 55
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'stand/assignment', $data)
            ->assertStatus(404);
    }

    public function testItReturnsConflictOnAssignmentIfStandAlreadyAssigned()
    {
        $this->doesntExpectEvents(StandAssignedEvent::class);
        $this->addStandAssignment('BAW123', 1);
        $data = [
            'callsign' => 'BAW9354',
            'stand_id' => 1
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'stand/assignment', $data)
            ->assertStatus(409);
    }

    public function testItDeletesStandAssignments()
    {
        $this->expectsEvents(StandUnassignedEvent::class);
        $this->addStandAssignment('BAW123', 1);
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, 'stand/assignment/BAW123')
            ->assertStatus(204);
    }

    public function testItDeletesStandAssignmentsIfNonePresent()
    {
        $this->doesntExpectEvents(StandUnassignedEvent::class);
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, 'stand/assignment/BAW123')
            ->assertStatus(204);
    }

    private function addStandAssignment(string $callsign, int $standId)
    {
        NetworkDataService::firstOrCreateNetworkAircraft($callsign);
        StandAssignment::create(
            [
                'callsign' => $callsign,
                'stand_id' => $standId,
            ]
        );
    }
}
