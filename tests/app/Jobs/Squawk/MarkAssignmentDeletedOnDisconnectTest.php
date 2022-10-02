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
    private SquawkService $squawkService;

    public function setUp() : void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        $this->squawkService = Mockery::mock(SquawkService::class);
        $this->app->instance(SquawkService::class, $this->squawkService);
        $this->listener = $this->app->make(MarkAssignmentDeletedOnDisconnect::class);
    }

    public function testItDeletesSquawkAssignments()
    {
        $this->squawkService->shouldReceive('deleteSquawkAssignment')->with('BAW123')->once();
        $this->listener->perform(NetworkAircraft::find('BAW123'));
    }
}
