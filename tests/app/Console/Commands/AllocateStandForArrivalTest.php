<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Services\StandService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Mockery;

class AllocateStandForArrivalTest extends BaseFunctionalTestCase
{
    public function testItCallsService()
    {
        Config::set('stands.auto_allocate', true);
        $serviceMock = Mockery::mock(StandService::class);
        $serviceMock->shouldReceive('allocateStandsForArrivals')->once();
        $this->app->instance(StandService::class, $serviceMock);

        Artisan::call('stands:assign-arrival');
    }

    public function testItDoesntAllocateStandsIfDisabled()
    {
        Config::set('stands.auto_allocate', false);
        $serviceMock = Mockery::mock(StandService::class);
        $serviceMock->shouldNotReceive('allocateStandForAircraft');
        $this->app->instance(StandService::class, $serviceMock);

        Artisan::call('stands:assign-arrival');
    }
}
