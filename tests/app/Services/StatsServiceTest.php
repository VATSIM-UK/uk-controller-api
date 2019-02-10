<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StatsServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var StatsService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = $this->app->make(StatsService::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(StatsService::class, $this->service);
    }

    public function testItReturnsData()
    {
        Carbon::setTestNow(Carbon::now());
        $expected = [
            'latest_plugin_version' => '2.0.1',
            'total_users' => 3,
            'active_users' => 2,
            'users_today' => 1,
            'active_users_latest_version' => 1,
            'current_squawks_assigned' => 4,
            'squawks_assigned_3_mo' => 2,
            'timestamp' => Carbon::now()->toDateTimeString(),
        ];
        $this->assertEquals($expected, $this->service->getStats());
    }

    public function testItCachesData()
    {
        Carbon::setTestNow(Carbon::now());
        $expected = [
            'latest_plugin_version' => '2.0.1',
            'total_users' => 3,
            'active_users' => 2,
            'users_today' => 1,
            'active_users_latest_version' => 1,
            'current_squawks_assigned' => 4,
            'squawks_assigned_3_mo' => 2,
            'timestamp' => Carbon::now()->toDateTimeString(),
        ];

        Cache::shouldReceive('has')
            ->once()
            ->with(StatsService::STATS_CACHE_KEY)
            ->andReturn(false);

        Cache::shouldReceive('add')
            ->once()
            ->with(StatsService::STATS_CACHE_KEY, $expected, StatsService::STATS_CACHE_TIME)
            ->andReturn(true);

        $this->service->getStats();
    }

    public function testItPullsDataFromCache()
    {
        $expected = [
            'foo' => 'bar',
        ];

        Cache::shouldReceive('has')
            ->once()
            ->with(StatsService::STATS_CACHE_KEY)
            ->andReturn(true);

        Cache::shouldReceive('get')
            ->once()
            ->with(StatsService::STATS_CACHE_KEY)
            ->andReturn($expected);

        $this->assertEquals($expected, $this->service->getStats());
    }
}
