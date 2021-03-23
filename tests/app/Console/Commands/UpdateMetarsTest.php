<?php

namespace App\Console\Commands;

use App\BaseUnitTestCase;
use App\Services\MetarService;
use Mockery;

class UpdateMetarsTest extends BaseUnitTestCase
{
    private UpdateMetars $command;

    public function setUp(): void
    {
        parent::setUp();
        $this->command = $this->app->make(UpdateMetars::class);
    }

    public function testItCallsMetarUpdate()
    {
        $mockService = Mockery::mock(MetarService::class);
        $mockService->expects('updateAllMetars')->withNoArgs()->once();
        $this->command->handle($mockService);
    }
}
