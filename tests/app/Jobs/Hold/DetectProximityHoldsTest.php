<?php

namespace App\Jobs\Hold;

use App\BaseUnitTestCase;
use App\Services\HoldService;
use App\Services\PrenoteMessageService;
use Mockery;

class DetectProximityHoldsTest extends BaseUnitTestCase
{
    private DetectProximityToHolds $detect;

    public function setUp(): void
    {
        parent::setUp();
        $this->detect = $this->app->make(DetectProximityToHolds::class);
    }

    public function testHandleCallsStandService()
    {
        $holdService = Mockery::mock(HoldService::class);
        $holdService->expects('checkAircraftHoldProximity')->withNoArgs()->once();
        $this->detect->handle($holdService);
    }
}
