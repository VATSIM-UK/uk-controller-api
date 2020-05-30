<?php

namespace App\Listeners\Hold;

use App\BaseFunctionalTestCase;
use App\Events\HoldAssignedEvent;
use App\Models\Hold\AssignedHold;
use App\Models\User\User;
use Carbon\Carbon;

class RecordHoldAssignmentTest extends BaseFunctionalTestCase
{
    /**
     * @var RecordHoldAssignment
     */
    private $listener;

    public function setUp() : void
    {
        parent::setUp();
        $this->listener = $this->app->make(RecordHoldAssignment::class);
    }

    public function testItRecordsHoldAssignment()
    {
        $this->actingAs(User::findOrFail(self::ACTIVE_USER_CID));
        Carbon::setTestNow(Carbon::now());
        $this->assertDatabaseMissing('hold_assignments_history', ['callsign' => 'NAX5XX']);
        $assignment = AssignedHold::new(
            [
                'callsign' => 'NAX5XX',
                'navaid_id' => 1,
                'created_at' => Carbon::now(),
            ]
        );
        $this->listener->handle(new HoldAssignedEvent($assignment));

        $this->assertDatabaseHas(
            'hold_assignments_history',
            [
                'callsign' => $assignment->callsign,
                'navaid_id' => $assignment->navaid_id,
                'allocated_at' => $assignment->allocated_at,
                'allocated_by' => self::ACTIVE_USER_CID,
            ]
        );
    }

    public function testItStopsEventPropogation()
    {
        $assignment = AssignedHold::new(
            [
                'callsign' => 'NAX5XX',
                'navaid_id' => 1,
                'created_at' => Carbon::now(),
            ]
        );
        $this->assertFalse($this->listener->handle(new HoldAssignedEvent($assignment)));
    }
}
