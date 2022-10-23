<?php

namespace App\Services\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandAssignmentsHistory;
use App\Models\User\User;

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
        ]);
        $history2 = StandAssignmentsHistory::create([
            'callsign' => 'BAW123',
            'stand_id' => 2,
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
        $this->service->createHistoryItem($assignment);
        $this->assertDatabaseHas(
            'stand_assignments_history',
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
                'user_id' => null,
            ]
        );
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
        $this->service->createHistoryItem($assignment);
        $this->assertDatabaseHas(
            'stand_assignments_history',
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
                'user_id' => self::ACTIVE_USER_CID,
            ]
        );
    }

    public function testCreatingAHistoryItemDeletesOtherHistory()
    {
        $history = StandAssignmentsHistory::create([
            'callsign' => 'BAW123',
            'stand_id' => 1,
        ]);
        $history2 = StandAssignmentsHistory::create([
            'callsign' => 'BAW123',
            'stand_id' => 2,
        ]);
        $assignment = StandAssignment::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
            ]
        );
        $this->service->createHistoryItem($assignment);

        $this->assertSoftDeleted($history->refresh());
        $this->assertSoftDeleted($history2->refresh());
    }
}
