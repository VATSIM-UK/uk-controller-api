<?php

namespace App\Console\Commands;

use App\BaseUnitTestCase;
use App\Services\MetarService;
use Mockery;

class UpdateMetarsTest extends BaseUnitTestCase
{
    private UpdateMetars $command;
    private MetarService $metarService;

    public function setUp(): void
    {
        parent::setUp();
        $this->metarService = Mockery::mock(MetarService::class);
        $this->app->instance(MetarService::class, $this->metarService);
        $this->command = $this->app->make(UpdateMetars::class);
    }

    public function testItCallsMetarUpdate()
    {
        $this->metarService->expects('updateAllMetars')->withNoArgs()->once();
        $this->artisan('metars:update');
    }
}
