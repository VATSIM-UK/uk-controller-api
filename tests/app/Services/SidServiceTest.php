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

    public function testItGetsASid()
    {
        $expected = [
            'id' => 1,
            'identifier' => 'TEST1X',
            'airfield_id' => 1,
            'handoff_id' => null,
            'initial_altitude' => 3000,
        ];
        $this->assertEquals($expected, $this->service->getSid(1));
    }

    public function testItReturnsNullOnSidNotFound()
    {
        $this->assertNull($this->service->getSid(55));
    }

    public function testItGetsAllSids()
    {
        $expected = [
                [
                    'id' => 1,
                    'identifier' => 'TEST1X',
                    'airfield_id' => 1,
                    'handoff_id' => null,
                    'initial_altitude' => 3000,
                ],
                [
                    'id' => 2,
                    'identifier' => 'TEST1Y',
                    'airfield_id' => 1,
                    'handoff_id' => null,
                    'initial_altitude' => 4000,
                ],
                [
                    'id' => 3,
                    'identifier' => 'TEST1A',
                    'airfield_id' => 2,
                    'handoff_id' => null,
                    'initial_altitude' => 5000,
                ],
            ];
        $this->assertEquals($expected, $this->service->getAllSids());
    }

    public function testItDeletesSids()
    {
        $this->assertDatabaseHas('sids', ['id' => 1]);
        $this->service->deleteSid(1);
        $this->assertDatabaseMissing('sids', ['id' => 1]);
    }

    public function testDeletingSidsReturnsTrue()
    {
        $this->assertTrue($this->service->deleteSid(1));
    }

    public function testDeletingNonExsistantSidsReturnsFalse()
    {
        $this->assertFalse($this->service->deleteSid(55));
    }

    public function testItAddsNewSids()
    {
        $this->service->createSid(1, 'TEST1M', 3000);
        $this->assertDatabaseHas('sids', ['identifier' => 'TEST1M', 'initial_altitude' => 3000, 'airfield_id' => 1]);
    }

    public function testAddingSidClearsDependencyCache()
    {
        Cache::shouldReceive('forget')
            ->with(SidService::DEPENDENCY_CACHE_KEY)
            ->once();
        $this->service->createSid(1, 'TEST1M', 3000);
    }

    public function testItUpdatesSids()
    {
        $this->service->updateSid(1, 2, 'TEST1M', 55000);
        $this->assertDatabaseHas(
            'sids',
            [
                'id' => 1,
                'identifier' => 'TEST1M',
                'initial_altitude' => 55000,
                'airfield_id' => 2,
            ]
        );
    }

    public function testUpdatingSidsClearsDependencyCache()
    {
        Cache::shouldReceive('forget')
            ->with(SidService::DEPENDENCY_CACHE_KEY)
            ->once();
        $this->service->updateSid(1, 2, 'TEST1M', 55000);
    }
}
