<?php

namespace App\Jobs\Hold;

use App\BaseUnitTestCase;
use App\Services\HoldService;
use Mockery;

class RemoveAssignmentsForAircraftLeavingHoldTest extends BaseUnitTestCase
{
    private RemoveAssignmentsForAircraftLeavingHold $occupy;

    public function setUp(): void
    {
        parent::setUp();
        $this->occupy = $this->app->make(RemoveAssignmentsForAircraftLeavingHold::class);
    }

    public function testHandleCallsStandService()
    {
        $holdService = Mockery::mock(HoldService::class);
        $holdService->expects('removeStaleAssignments')->withNoArgs()->once();
        $this->occupy->handle($holdService);
    }
}
