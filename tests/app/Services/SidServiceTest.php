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
}
