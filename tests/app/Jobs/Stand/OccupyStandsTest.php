<?php

namespace App\Jobs\Stand;

use App\BaseUnitTestCase;
use App\Services\Stand\StandOccupationService;
use Mockery;

class OccupyStandsTest extends BaseUnitTestCase
{
    private OccupyStands $occupy;

    public function setUp(): void
    {
        parent::setUp();
        $this->occupy = $this->app->make(OccupyStands::class);
    }

    public function testHandleCallsStandService()
    {
        $standService = Mockery::mock(StandOccupationService::class);
        $standService->expects('setOccupiedStands')->withNoArgs()->once();
        $this->occupy->handle($standService);
    }
}
