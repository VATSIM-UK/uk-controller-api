<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Services\StatsService;
use Illuminate\Support\Facades\Cache;

class StatsControllerTest extends BaseApiTestCase
{
    public function testItConstructs()
    {
        $this->assertInstanceOf(StatsController::class, $this->app->make(StatsController::class));
    }

    public function testItReturns200OnSuccessfulRequest()
    {
        Cache::shouldReceive('has')
            ->with(StatsService::STATS_CACHE_KEY)
            ->andReturn(true);

        Cache::shouldReceive('get')
            ->with(StatsService::STATS_CACHE_KEY)
            ->andReturn(['foo' => 'bar']);

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'stats')->seeStatusCode(200);
    }

    public function testItReturnsStatsData()
    {
        Cache::shouldReceive('has')
            ->with(StatsService::STATS_CACHE_KEY)
            ->andReturn(true);

        Cache::shouldReceive('get')
            ->with(StatsService::STATS_CACHE_KEY)
            ->andReturn(['foo' => 'bar']);

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'stats')->seeJson(['foo' => 'bar']);
    }
}
