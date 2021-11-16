<?php

namespace App\Jobs\Network;

use App\BaseUnitTestCase;
use App\Services\NetworkControllerService;
use Mockery;

class IdentifyActiveControllerPositionsTest extends BaseUnitTestCase
{
    private IdentifyActiveControllerPositions $identify;

    public function setUp(): void
    {
        parent::setUp();
        $this->identify = $this->app->make(IdentifyActiveControllerPositions::class);
    }

    public function testHandleCallsStandService()
    {
        $controllerService = Mockery::mock(NetworkControllerService::class);
        $controllerService->expects('updatedMatchedControllerPositions')->withNoArgs()->once();
        $this->identify->handle($controllerService);
    }
}
