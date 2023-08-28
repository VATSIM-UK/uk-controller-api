<?php

namespace App\Services\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandAssignmentsHistory;
use App\Models\User\User;
use App\Services\NetworkAircraftService;

class StandAssignmentsHistoryServiceTest extends BaseFunctionalTestCase
{
    private readonly StandAssignmentsHistoryService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(StandAssignmentsHistoryService::class);
    }

    public function testItDeletesAHistoryForAnAssignment()
    {
        $history = StandAssignmentsHistory::create([
            'callsign' => 'BAW123',
            'stand_id' => 1,
            'type' => 'test',
        ]);
        $history2 = StandAssignmentsHistory::create([
            'callsign' => 'BAW123',
            'stand_id' => 2,
            'type' => 'test',
        ]);
        $assignment = StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );

        $this->service->deleteHistoryFor($assignment);
        $this->assertSoftDeleted($history->refresh());
        $this->assertSoftDeleted($history2->refresh());
    }

    public function testItCreatesStandAssignmentHistory()
    {
        $assignment = StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );
        $this->service->createHistoryItem(new StandAssignmentContext($assignment, 'test', collect()));
        $this->assertDatabaseHas(
            'stand_assignments_history',
            [
                'callsign' => 'BAW123',
                'type' => 'test',
                'stand_id' => 1,
                'user_id' => null,
            ],
        );

        $expectedContext = [
            'aircraft_type' => 'B738',
            'aircraft_departure_airfield' => 'EGKK',
            'aircraft_arrival_airfield' => 'EGLL',
            'removed_assignments' => [],
            'occupied_stands' => [],
            'assigned_stands' => [],
        ];
        $context = StandAssignmentsHistory::latest()->first()->context;
        $this->assertEquals($expectedContext, $context);
    }

    public function testItCreatesStandAssignmentHistoryWithContext()
    {
        $newStand = Stand::factory()->create(['airfield_id' => 1]);
        $assignment = StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => $newStand->id,
            ]
        );

        // Create some context to the assignment - stand assignments
        NetworkAircraftService::createPlaceholderAircraft('BAW999');
        NetworkAircraftService::createPlaceholderAircraft('BAW1000');
        NetworkAircraftService::createPlaceholderAircraft('BAW1001');
        NetworkAircraftService::createPlaceholderAircraft('BAW1002');
        $existing1 = StandAssignment::create(
            [
                'callsign' => 'BAW999',
                'stand_id' => 1,
            ]
        );
        $existing2 = StandAssignment::create(
            [
                'callsign' => 'BAW1000',
                'stand_id' => 2,
            ]
        );

        $removedAssignments = collect([$existing1, $existing2]);

        // Extra context - occupations
        $occupiedStand = Stand::factory()->create(['airfield_id' => 1]);
        $occupiedStand->occupier()->sync(['BAW1001']);
        $occupiedStand2 = Stand::factory()->create(['airfield_id' => 1]);
        $occupiedStand2->occupier()->sync(['BAW1002']);

        $this->service->createHistoryItem(new StandAssignmentContext($assignment, 'test', $removedAssignments));
        $this->assertDatabaseHas(
            'stand_assignments_history',
            [
                'callsign' => 'BAW123',
                'stand_id' => $newStand->id,
                'type' => 'test',
                'user_id' => null,
            ],
        );

        // Assign a stand at another airfield to test this isn't in the response
        $standAtAnotherAirfield = Stand::factory()->create();
        NetworkAircraftService::createPlaceholderAircraft('BAW1003');
        $standAtAnotherAirfield->assignment()->create(['callsign' => 'BAW1003']);

        // Occupy a stand at another airfield to test this isn't in the response
        $standAtAnotherAirfield2 = Stand::factory()->create();
        NetworkAircraftService::createPlaceholderAircraft('BAW1004');
        $standAtAnotherAirfield2->occupier()->sync(['BAW1004']);


        $expectedContext = [
            'aircraft_type' => 'B738',
            'aircraft_departure_airfield' => 'EGKK',
            'aircraft_arrival_airfield' => 'EGLL',
            'removed_assignments' => [
                [
                    'callsign' => 'BAW999',
                    'stand' => '1L',
                ],
                [
                    'callsign' => 'BAW1000',
                    'stand' => '251',
                ],
            ],
            'occupied_stands' => [
                $occupiedStand->identifier,
                $occupiedStand2->identifier,
            ],
            'assigned_stands' => [
                '1L',
                '251',
            ],
        ];
        $context = StandAssignmentsHistory::latest()->first()->context;
        $this->assertEquals($expectedContext, $context);
    }

    public function testItCreatesStandAssignmentHistoryWithAUser()
    {
        $this->actingAs(User::findOrFail(self::ACTIVE_USER_CID));
        $assignment = StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );
        $this->service->createHistoryItem(new StandAssignmentContext($assignment, 'test', collect()));
        $this->assertDatabaseHas(
            'stand_assignments_history',
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
                'type' => 'test',
                'user_id' => self::ACTIVE_USER_CID,
            ]
        );

        $expectedContext = [
            'aircraft_type' => 'B738',
            'aircraft_departure_airfield' => 'EGKK',
            'aircraft_arrival_airfield' => 'EGLL',
            'removed_assignments' => [],
            'occupied_stands' => [],
            'assigned_stands' => [],
        ];
        $context = StandAssignmentsHistory::latest()->first()->context;
        $this->assertEquals($expectedContext, $context);
    }

    public function testCreatingAHistoryItemDeletesOtherHistory()
    {
        $history = StandAssignmentsHistory::create([
            'callsign' => 'BAW123',
            'stand_id' => 1,
            'type' => 'test',
        ]);
        $history2 = StandAssignmentsHistory::create([
            'callsign' => 'BAW123',
            'stand_id' => 2,
            'type' => 'test',
        ]);
        $assignment = StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );
        $this->service->createHistoryItem(new StandAssignmentContext($assignment, 'test', collect()));

        $this->assertSoftDeleted($history->refresh());
        $this->assertSoftDeleted($history2->refresh());
    }
}
