<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use Illuminate\Support\Facades\Cache;

class SidServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var SidService
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(SidService::class);
        Cache::forget(SidService::DEPENDENCY_CACHE_KEY);
    }

    public function testItGeneratesAndCachesSidDependency()
    {
        $expected = [
            'EGLL' => [
                'TEST1X' => 3000,
                'TEST1Y' => 4000,
            ],
            'EGBB' => [
                'TEST1A' => 5000,
            ],
        ];

        Cache::shouldReceive('has')
            ->with(SidService::DEPENDENCY_CACHE_KEY)
            ->once()
            ->andReturn(false);

        Cache::shouldReceive('forever')
            ->with(SidService::DEPENDENCY_CACHE_KEY, $expected)
            ->once();

        $this->service->getInitialAltitudeDependency();
    }

    public function testItReturnsCachedDependency()
    {
        $expected = [
            'EGLL' => [
                'TEST1X' => 3000,
                'TEST1Y' => 4000,
            ],
            'EGBB' => [
                'TEST1A' => 5000,
            ],
        ];

        Cache::shouldReceive('has')
            ->with(SidService::DEPENDENCY_CACHE_KEY)
            ->once()
            ->andReturn(true);

        Cache::shouldReceive('get')
            ->with(SidService::DEPENDENCY_CACHE_KEY)
            ->once()
            ->andReturn($expected);

        $this->assertEquals($expected, $this->service->getInitialAltitudeDependency());
    }

    public function testItDeletesSids()
    {
        $this->assertDatabaseHas('sid', ['id' => 1]);
        $this->service->deleteSid(1);
        $this->assertDatabaseMissing('sid', ['id' => 1]);
    }

    public function testItAddsNewSids()
    {
        $this->service->createSid(1, 'TEST1M', 3000);
        $this->assertDatabaseHas('sid', ['identifier' => 'TEST1M', 'initial_altitude' => 3000, 'airfield_id' => 1]);
    }

    public function testItUpdatesSids()
    {
        $this->service->updateSid(1, 2, 'TEST1M', 55000);
        $this->assertDatabaseHas(
            'sid',
            [
                'id' => 1,
                'identifier' => 'TEST1M',
                'initial_altitude' => 55000,
                'airfield_id' => 2
            ]
        );
    }
}
