<?php

namespace App\Jobs\Squawk;

use App\BaseUnitTestCase;
use App\Services\SquawkService;
use Mockery;

class ReserveActiveSquawkTest extends BaseUnitTestCase
{
    private ReserveActiveSquawks $assign;

    public function setUp(): void
    {
        parent::setUp();
        $this->assign = $this->app->make(ReserveActiveSquawks::class);
    }

    public function testHandleCallsStandService()
    {
        $squawkService = Mockery::mock(SquawkService::class);
        $squawkService->expects('reserveSquawksInFirProximity')->withNoArgs()->once();
        $this->assign->handle($squawkService);
    }
}
