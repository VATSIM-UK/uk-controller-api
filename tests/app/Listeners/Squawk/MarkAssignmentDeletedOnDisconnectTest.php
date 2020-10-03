<?php

namespace App\Listeners\Squawk;

use App\BaseFunctionalTestCase;
use App\Events\NetworkAircraftDisconnectedEvent;
use App\Models\Squawk\Ccams\CcamsSquawkAssignment;
use App\Models\Squawk\SquawkAssignmentsHistory;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;

class MarkAssignmentDeletedOnDisconnectTest extends BaseFunctionalTestCase
{
    /**
     * @var MarkAssignmentDeletedOnDisconnect
     */
    private $listener;

    public function setUp() : void
    {
        parent::setUp();
        $this->listener = $this->app->make(MarkAssignmentDeletedOnDisconnect::class);
        Carbon::setTestNow(Carbon::now());
    }

    public function testItMarksHistoryAsDeleted()
    {
        CcamsSquawkAssignment::create(['callsign' => 'BAW123', 'code' => '0123']);
        SquawkAssignmentsHistory::create(
            [
                'callsign' => 'BAW123',
                'code' => '0001',
                'type' => 'CCAMS',
                'user_id' => null,
            ]
        );

        $this->listener->handle(new NetworkAircraftDisconnectedEvent(new NetworkAircraft(['callsign' => 'BAW123'])));
        $this->assertSoftDeleted(
            'squawk_assignments_history',
            [
                'callsign' => 'BAW123',
            ]
        );
    }

    public function testItDoesntDoubleDeleteAssignmentHistory()
    {
        CcamsSquawkAssignment::create(['callsign' => 'BAW123', 'code' => '0123']);
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

        $this->listener->handle(new NetworkAircraftDisconnectedEvent(new NetworkAircraft(['callsign' => 'BAW123'])));
        $this->assertDatabaseHas(
            'squawk_assignments_history',
            [
                'callsign' => 'BAW123',
                'deleted_at' => '2020-05-01 00:11:22',
            ]
        );
    }

    public function testItDeletesAssignment()
    {
        CcamsSquawkAssignment::create(['callsign' => 'BAW123', 'code' => '0123']);
        $this->listener->handle(new NetworkAircraftDisconnectedEvent(new NetworkAircraft(['callsign' => 'BAW123'])));
        $this->assertNull(CcamsSquawkAssignment::find('BAW123'));
    }

    public function testItContinuesPropagation()
    {
        $this->assertTrue(
            $this->listener->handle(new NetworkAircraftDisconnectedEvent(new NetworkAircraft(['callsign' => 'BAW123'])))
        );
    }
}
