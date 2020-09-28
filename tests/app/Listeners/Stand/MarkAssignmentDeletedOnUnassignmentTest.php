<?php

namespace App\Listeners\Stand;

use App\BaseFunctionalTestCase;
use App\Events\StandUnassignedEvent;
use App\Models\Stand\StandAssignmentsHistory;

class MarkAssignmentDeletedOnUnassignmentTest extends BaseFunctionalTestCase
{
    /**
     * @var MarkAssignmentDeletedOnUnassignment
     */
    private $listener;

    public function setUp() : void
    {
        parent::setUp();
        $this->listener = $this->app->make(MarkAssignmentDeletedOnUnassignment::class);
    }

    public function testItMarksHistoryAsDeleted()
    {
        StandAssignmentsHistory::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
                'user_id' => null,
            ]
        );
        StandAssignmentsHistory::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 2,
                'user_id' => null,
            ]
        );
        StandAssignmentsHistory::create(
            [
                'callsign' => 'BAW456',
                'stand_id' => 3,
                'user_id' => null,
            ]
        );

        $this->listener->handle(new StandUnassignedEvent('BAW123'));
        $this->assertSoftDeleted(
            'stand_assignments_history',
            [
                'callsign' => 'BAW123',
            ]
        );
        $this->assertDatabaseHas(
            'stand_assignments_history',
            [
                'callsign' => 'BAW456',
                'stand_id' => 3,
                'user_id' => null,
                'deleted_at' => null,
            ]
        );
    }

    public function testItDoesntDoubleDelete()
    {
        $deletedHistory = StandAssignmentsHistory::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
                'user_id' => null,
            ]
        );
        $deletedHistory->deleted_at = '2020-05-01 00:11:22';
        $deletedHistory->save();

        $this->listener->handle(new StandUnassignedEvent('BAW123'));
        $this->assertDatabaseHas(
            'stand_assignments_history',
            [
                'callsign' => 'BAW123',
                'deleted_at' => '2020-05-01 00:11:22',
            ]
        );
    }

    public function testItContinuesPropagation()
    {
        $this->assertTrue(
            $this->listener->handle(new StandUnassignedEvent('BAW123'))
        );
    }
}
