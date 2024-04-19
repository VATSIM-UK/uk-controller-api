<?php

namespace App\Jobs\Stand;

use App\BaseUnitTestCase;
use App\Services\Stand\ArrivalAllocationService;
use Mockery;

class RemoveDisconnectedArrivalStandsTest extends BaseUnitTestCase
{
    private RemoveDisconnectedArrivalStands $assign;

    public function setUp(): void
    {
        parent::setUp();
        $this->assign = $this->app->make(RemoveDisconnectedArrivalStands::class);
    }

    public function testHandleCallsStandService()
    {
        $standService = Mockery::mock(ArrivalAllocationService::class);
        $standService->expects('removeArrivalStandsFromDisconnectedAircraft')->withNoArgs()->once();
        $this->assign->handle($standService);
    }
}
