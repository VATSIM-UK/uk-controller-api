<?php

namespace App\Listeners\Squawk;

use App\BaseFunctionalTestCase;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Events\SquawkAssignmentEvent;
use App\Events\SquawkUnassignedEvent;
use App\Models\Squawk\Ccams\CcamsSquawkAssignment;
use App\Models\Squawk\SquawkAssignmentsHistory;
use App\Models\Squawks\Allocation;
use App\Models\User\User;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;

class MarkAssignmentDeletedOnUnassignmentTest extends BaseFunctionalTestCase
{
    /**
     * @var MarkAssignmentHistoryDeletedOnUnassignment
     */
    private $listener;

    public function setUp() : void
    {
        parent::setUp();
        $this->listener = $this->app->make(MarkAssignmentHistoryDeletedOnUnassignment::class);
        Carbon::setTestNow(Carbon::now());
    }

    public function testItMarksHistoryAsDeleted()
    {
        SquawkAssignmentsHistory::create(
            [
                'callsign' => 'BAW123',
                'code' => '0001',
                'type' => 'CCAMS',
                'user_id' => null,
            ]
        );

        $this->listener->handle(new SquawkUnassignedEvent('BAW123'));
        $this->assertSoftDeleted(
            'squawk_assignments_history',
            [
                'callsign' => 'BAW123',
            ]
        );
    }

    public function testItDoesntDoubleDelete()
    {
        $deletedHistory = SquawkAssignmentsHistory::create(
            [
                'callsign' => 'BAW123',
                'code' => '0001',
                'type' => 'CCAMS',
                'user_id' => null,
            ]
        );
        $deletedHistory->deleted_at = '2020-05-01 00:11:22';
        $deletedHistory->save();

        $this->listener->handle(new SquawkUnassignedEvent('BAW123'));
        $this->assertDatabaseHas(
            'squawk_assignments_history',
            [
                'callsign' => 'BAW123',
                'deleted_at' => '2020-05-01 00:11:22',
            ]
        );
    }
}
