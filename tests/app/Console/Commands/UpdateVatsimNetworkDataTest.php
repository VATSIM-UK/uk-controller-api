<?php

namespace App\Console\Commands;

use App\BaseUnitTestCase;
use App\Services\NetworkAircraftService;
use Illuminate\Support\Facades\Artisan;
use Mockery;

class UpdateVatsimNetworkDataTest extends BaseUnitTestCase
{
    public function testItRunsDataUpdate()
    {
        $mockService = Mockery::mock(NetworkAircraftService::class);
        $mockService->shouldReceive('updateNetworkData')->once();
        $this->app->instance(NetworkAircraftService::class, $mockService);

        Artisan::call('networkdata:update');
    }
}
