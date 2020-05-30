<?php

namespace App\Listeners\Hold;

use App\BaseFunctionalTestCase;
use App\Events\HoldUnassignedEvent;
use App\Models\Hold\AssignedHold;
use App\Models\User\User;
use Carbon\Carbon;

class RecordHoldUnassignmentTest extends BaseFunctionalTestCase
{
    /**
     * @var RecordHoldUnassignment
     */
    private $listener;

    public function setUp() : void
    {
        parent::setUp();
        $this->listener = $this->app->make(RecordHoldUnassignment::class);
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
        $this->listener->handle(new HoldUnassignedEvent($assignment));

        $this->assertDatabaseHas(
            'hold_assignments_history',
            [
                'callsign' => $assignment->callsign,
                'navaid_id' => $assignment->navaid_id,
                'allocated_at' => null,
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
        $this->assertFalse($this->listener->handle(new HoldUnassignedEvent($assignment)));
    }
}
