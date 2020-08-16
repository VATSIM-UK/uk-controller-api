<?php

namespace App\Listeners\Stand;

use App\BaseFunctionalTestCase;
use App\Events\StandAssignedEvent;
use App\Models\Stand\StandAssignment;
use App\Models\User\User;
use Carbon\Carbon;

class RecordStandAssignmentHistoryTest extends BaseFunctionalTestCase
{
    /**
     * @var RecordStandAssignmentHistory
     */
    private $listener;

    public function setUp() : void
    {
        parent::setUp();
        $this->listener = $this->app->make(RecordStandAssignmentHistory::class);
        Carbon::setTestNow(Carbon::now());
    }

    public function testItCreatesAnAssignmentHistory()
    {
        $this->actingAs(User::find(self::ACTIVE_USER_CID));
        $assignment = new StandAssignedEvent(
            new StandAssignment(
                [
                    'callsign' => 'BAW123',
                    'stand_id' => 1
                ]
            )
        );
        $this->listener->handle($assignment);

        $this->assertDatabaseHas(
            'stand_assignments_history',
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
                'user_id' => self::ACTIVE_USER_CID,
            ]
        );
    }

    public function testItCreatesAnAssignmentHistoryNoUser()
    {
        $assignment = new StandAssignedEvent(
            new StandAssignment(
                [
                    'callsign' => 'BAW123',
                    'stand_id' => 1
                ]
            )
        );
        $this->listener->handle($assignment);

        $this->assertDatabaseHas(
            'stand_assignments_history',
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
                'user_id' => null,
            ]
        );
    }

    public function testItContinuesPropagation()
    {
        $assignment = new StandAssignedEvent(
            new StandAssignment(
                [
                    'callsign' => 'BAW123',
                    'stand_id' => 1
                ]
            )
        );
        $this->assertTrue($this->listener->handle($assignment));
    }
}
