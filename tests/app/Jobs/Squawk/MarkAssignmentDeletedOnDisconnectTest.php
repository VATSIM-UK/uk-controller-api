<?php

namespace App\Jobs\Squawk;

use App\BaseFunctionalTestCase;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\SquawkService;
use Carbon\Carbon;
use Mockery;

class MarkAssignmentDeletedOnDisconnectTest extends BaseFunctionalTestCase
{
    private MarkAssignmentDeletedOnDisconnect $listener;
    private $squawkService;

    public function setUp() : void
    {
        parent::setUp();
        $this->listener = new MarkAssignmentDeletedOnDisconnect(new NetworkAircraft(['callsign' => 'BAW123']));
        $this->squawkService = Mockery::mock(SquawkService::class);
        Carbon::setTestNow(Carbon::now());
    }

    public function testItDeletesSquawkAssignments()
    {
        $this->squawkService->shouldReceive('deleteSquawkAssignment')->with('BAW123');
        $this->listener->handle($this->squawkService);
    }
}
