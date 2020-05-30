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
        Carbon::setTestNow(Carbon::now());
        $this->actingAs(User::findOrFail(self::ACTIVE_USER_CID));
    }

    private function getAssignment(): AssignedHold
    {
        $assignment = new AssignedHold(
            [
                'callsign' => 'NAX5XX',
                'navaid_id' => 1,
            ]
        );
        $assignment->created_at = Carbon::now();
        return $assignment;
    }

    public function testItRecordsUnHoldAssignment()
    {
        Carbon::setTestNow(Carbon::now());
        $this->assertDatabaseMissing('assigned_holds_history', ['callsign' => 'NAX5XX']);
        $this->listener->handle(new HoldUnassignedEvent($this->getAssignment()));

        $this->assertDatabaseHas(
            'assigned_holds_history',
            [
                'callsign' => 'NAX5XX',
                'navaid_id' => null,
                'assigned_at' => Carbon::now(),
                'assigned_by' => self::ACTIVE_USER_CID,
            ]
        );
    }

    public function testItPrefersUpdatedAtTime()
    {
        Carbon::setTestNow(Carbon::now());
        $this->assertDatabaseMissing('assigned_holds_history', ['callsign' => 'NAX5XX']);
        $assignment = $this->getAssignment();
        $assignment->setUpdatedAt(Carbon::now()->addHour());
        $this->listener->handle(new HoldUnassignedEvent($assignment));

        $this->assertDatabaseHas(
            'assigned_holds_history',
            [
                'callsign' => 'NAX5XX',
                'navaid_id' => null,
                'assigned_at' => Carbon::now()->addHour(),
                'assigned_by' => self::ACTIVE_USER_CID,
            ]
        );
    }

    public function testItStopsEventPropogation()
    {
        $this->assertFalse($this->listener->handle(new HoldUnassignedEvent($this->getAssignment())));
    }
}
