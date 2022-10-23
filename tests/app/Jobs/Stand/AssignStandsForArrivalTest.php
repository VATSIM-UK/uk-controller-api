<?php

namespace App\Jobs\Stand;

use App\BaseUnitTestCase;
use App\Services\Stand\ArrivalAllocationService;
use Mockery;

class AssignStandsForArrivalTest extends BaseUnitTestCase
{
    private AssignStandsForArrival $assign;

    public function setUp(): void
    {
        parent::setUp();
        $this->assign = $this->app->make(AssignStandsForArrival::class);
    }

    public function testHandleCallsStandService()
    {
        $standService = Mockery::mock(ArrivalAllocationService::class);
        $standService->expects('allocateStandsAtArrivalAirfields')->withNoArgs()->once();
        $this->assign->handle($standService);
    }
}
