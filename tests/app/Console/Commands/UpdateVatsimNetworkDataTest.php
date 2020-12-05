<?php

namespace App\Console\Commands;

use App\BaseUnitTestCase;
use App\Services\NetworkDataService;
use Illuminate\Support\Facades\Artisan;
use Mockery;

class UpdateVatsimNetworkDataTest extends BaseUnitTestCase
{
    public function testItRunsDataUpdate()
    {
        $mockService = Mockery::mock(NetworkDataService::class);
        $mockService->shouldReceive('updateNetworkData')->once();
        $this->app->instance(NetworkDataService::class, $mockService);

        Artisan::call('networkdata:update');
    }
}
