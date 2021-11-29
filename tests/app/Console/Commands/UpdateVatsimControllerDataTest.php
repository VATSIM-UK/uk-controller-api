<?php

namespace App\Console\Commands;

use App\BaseUnitTestCase;
use App\Services\NetworkControllerService;
use Illuminate\Support\Facades\Artisan;
use Mockery;

class UpdateVatsimControllerDataTest extends BaseUnitTestCase
{
    public function testItRunsDataUpdate()
    {
        $mockService = Mockery::mock(NetworkControllerService::class);
        $mockService->shouldReceive('updateNetworkData')->once();
        $this->app->instance(NetworkControllerService::class, $mockService);

        Artisan::call('networkdata:update-controllers');
    }
}
