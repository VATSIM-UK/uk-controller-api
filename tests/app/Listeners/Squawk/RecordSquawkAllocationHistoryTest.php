<?php

namespace App\Listeners\Squawk;

use App\BaseFunctionalTestCase;
use App\Events\SquawkAllocationEvent;
use App\Models\Squawks\Allocation;
use Carbon\Carbon;

class RecordSquawkAllocationHistoryTest extends BaseFunctionalTestCase
{

    /**
     * @var RecordSquawkAllocationHistory
     */
    private $listener;

    public function setUp() : void
    {
        parent::setUp();
        $this->listener = $this->app->make(RecordSquawkAllocationHistory::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(RecordSquawkAllocationHistory::class, $this->listener);
    }

    public function testItCreatesAnAllocationHistory()
    {
        $this->notSeeInDatabase('squawk_allocation_history', ['callsign' => 'NAX5XX']);
        $allocation = Allocation::create(
            [
                'callsign' => 'NAX5XX',
                'squawk' => '7262',
                'allocated_at' => Carbon::now(),
                'allocated_by' => self::ACTIVE_USER_CID,
            ]
        );
        $this->listener->handle(new SquawkAllocationEvent($allocation));

        $this->seeInDatabase(
            'squawk_allocation_history',
            [
                'callsign' => $allocation->callsign,
                'squawk' => $allocation->squawk,
                'allocated_at' => $allocation->allocated_at,
                'allocated_by' => $allocation->allocated_by,
                'new' => true,
            ]
        );
    }

    public function testItStopsEventPropogation()
    {
        $allocation = Allocation::create(
            [
                'callsign' => 'NAX5XX',
                'squawk' => '7262',
                'allocated_at' => Carbon::now(),
                'allocated_by' => self::ACTIVE_USER_CID,
            ]
        );
        $this->assertFalse($this->listener->handle(new SquawkAllocationEvent($allocation)));
    }
}
