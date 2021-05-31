<?php

namespace App\Jobs\Release\Departure;

use App\BaseUnitTestCase;
use App\Services\DepartureReleaseService;
use Mockery;

class CancelRequestsForDepartedAircraftTest extends BaseUnitTestCase
{
    private CancelRequestsForDepartedAircraft $cancel;

    public function setUp(): void
    {
        parent::setUp();
        $this->cancel = $this->app->make(CancelRequestsForDepartedAircraft::class);
    }

    public function testHandleCallsStandService()
    {
        $releaseService = Mockery::mock(DepartureReleaseService::class);
        $releaseService->expects('cancelReleasesForAirborneAircraft')->withNoArgs()->once();
        $this->cancel->handle($releaseService);
    }
}
