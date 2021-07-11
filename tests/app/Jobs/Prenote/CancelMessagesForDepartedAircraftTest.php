<?php

namespace App\Jobs\Prenote;

use App\BaseUnitTestCase;
use App\Services\PrenoteMessageService;
use Mockery;

class CancelMessagesForDepartedAircraftTest extends BaseUnitTestCase
{
    private CancelMessagesForDepartedAircraft $cancel;

    public function setUp(): void
    {
        parent::setUp();
        $this->cancel = $this->app->make(CancelMessagesForDepartedAircraft::class);
    }

    public function testHandleCallsStandService()
    {
        $prenoteService = Mockery::mock(PrenoteMessageService::class);
        $prenoteService->expects('cancelMessagesForAirborneAircraft')->withNoArgs()->once();
        $this->cancel->handle($prenoteService);
    }
}
