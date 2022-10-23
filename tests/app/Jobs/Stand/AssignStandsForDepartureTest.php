<?php

namespace App\Jobs\Stand;

use App\BaseUnitTestCase;
use App\Services\Stand\DepartureAllocationService;
use App\Services\Stand\StandService;
use Mockery;

class AssignStandsForDepartureTest extends BaseUnitTestCase
{
    private AssignStandsForDeparture $assign;

    public function setUp(): void
    {
        parent::setUp();
        $this->assign = $this->app->make(AssignStandsForDeparture::class);
    }

    public function testHandleCallsStandService()
    {
        $standService = Mockery::mock(DepartureAllocationService::class);
        $standService->expects('assignStandsForDeparture')->withNoArgs()->once();
        $this->assign->handle($standService);
    }
}
