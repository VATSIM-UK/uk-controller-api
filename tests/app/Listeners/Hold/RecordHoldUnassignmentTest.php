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
    }

    public function testItRecordsUnHoldAssignment()
    {
        $this->actingAs(User::findOrFail(self::ACTIVE_USER_CID));
        Carbon::setTestNow(Carbon::now());
        $this->assertDatabaseMissing('assigned_holds_history', ['callsign' => 'NAX5XX']);
        $this->listener->handle(new HoldUnassignedEvent('NAX5XX'));

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

    public function testItHandlesSystemUnassignments()
    {
        Carbon::setTestNow(Carbon::now());
        $this->assertDatabaseMissing('assigned_holds_history', ['callsign' => 'NAX5XX']);
        $this->listener->handle(new HoldUnassignedEvent('NAX5XX'));

        $this->assertDatabaseHas(
            'assigned_holds_history',
            [
                'callsign' => 'NAX5XX',
                'navaid_id' => null,
                'assigned_at' => Carbon::now(),
                'assigned_by' => null,
            ]
        );
    }

    public function testItContinuesPropagation()
    {
        $this->assertTrue($this->listener->handle(
            new HoldUnassignedEvent($this->listener->handle(new HoldUnassignedEvent('NAX5XX'))))
        );
    }
}
