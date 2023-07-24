<?php

namespace App\Listeners\Squawk;

use App\BaseFunctionalTestCase;
use App\Events\SquawkAssignmentEvent;
use App\Models\Squawk\SquawkAssignment;
use App\Models\User\User;
use Carbon\Carbon;

class RecordSquawkAssignmentHistoryTest extends BaseFunctionalTestCase
{
    /**
     * @var RecordSquawkAssignmentHistory
     */
    private $listener;

    public function setUp() : void
    {
        parent::setUp();
        $this->listener = $this->app->make(RecordSquawkAssignmentHistory::class);
        Carbon::setTestNow(Carbon::now());
    }

    public function testItCreatesAnAssignmentHistory()
    {
        $this->actingAs(User::find(self::ACTIVE_USER_CID));
        $assignment = new SquawkAssignmentEvent(
            new SquawkAssignment(
                [
                    'callsign' => 'BAW123',
                    'code' => '0001',
                    'assignment_type' => 'CCAMS',
                ]
            )
        );
        $this->listener->handle($assignment);

        $this->assertDatabaseHas(
            'squawk_assignments_history',
            [
                'callsign' => 'BAW123',
                'code' => '0001',
                'allocated_at' => Carbon::now(),
                'user_id' => self::ACTIVE_USER_CID,
            ]
        );
    }

    public function testItCreatesAnAssignmentHistoryNoUser()
    {
        $assignment = new SquawkAssignmentEvent(
            new SquawkAssignment(
                [
                    'callsign' => 'BAW123',
                    'code' => '0001',
                    'assignment_type' => 'CCAMS',
                ]
            )
        );
        $this->listener->handle($assignment);

        $this->assertDatabaseHas(
            'squawk_assignments_history',
            [
                'callsign' => 'BAW123',
                'code' => '0001',
                'allocated_at' => Carbon::now(),
                'user_id' => null,
            ]
        );
    }
}
