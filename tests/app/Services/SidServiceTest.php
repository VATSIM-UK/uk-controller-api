<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Sid;

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

        $this->assertEquals($expected, $this->service->getInitialAltitudeDependency());
    }
    public function testItGetsASid()
    {
        $expected = [
            'id' => 1,
            'identifier' => 'TEST1X',
            'airfield_id' => 1,
            'handoff_id' => 1,
            'initial_altitude' => 3000,
            'sid_departure_interval_group_id' => null,
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
                'handoff_id' => 1,
                'initial_altitude' => 3000,
                'prenotes' => [
                    1,
                ],
                'sid_departure_interval_group_id' => null,
            ],
            [
                'id' => 2,
                'identifier' => 'TEST1Y',
                'airfield_id' => 1,
                'handoff_id' => 1,
                'initial_altitude' => 4000,
                'prenotes' => [],
                'sid_departure_interval_group_id' => null,
            ],
            [
                'id' => 3,
                'identifier' => 'TEST1A',
                'airfield_id' => 2,
                'handoff_id' => 2,
                'initial_altitude' => 5000,
                'prenotes' => [],
                'sid_departure_interval_group_id' => null,
            ],
        ];

        $this->assertEquals($expected, $this->service->getAllSids());
    }


public function testItDeletesSids()
    {
        $this->assertDatabaseHas('sid', ['id' => 1]);
        $this->service->deleteSid(1);
        $this->assertDatabaseMissing('sid', ['id' => 1]);
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
                'airfield_id' => 2,
                'sid_departure_interval_group_id' => null,
            ]
        );
    }

    public function testItGetsSidsDependency()
    {
        Sid::find(1)->update(['sid_departure_interval_group_id' => 1]);
        Sid::find(2)->update(['sid_departure_interval_group_id' => 2]);
        Sid::find(3)->update(['sid_departure_interval_group_id' => 3]);
        $expected = [
            [
                'id' => 1,
                'identifier' => 'TEST1X',
                'airfield' => 'EGLL',
                'handoff' => 1,
                'initial_altitude' => 3000,
                'departure_interval_group' => 1,
                'prenotes' => [
                    1,
                ],
            ],
            [
                'id' => 2,
                'airfield' => 'EGLL',
                'identifier' => 'TEST1Y',
                'handoff' => 1,
                'initial_altitude' => 4000,
                'prenotes' => [],
                'departure_interval_group' => 2,
            ],
            [
                'id' => 3,
                'airfield' => 'EGBB',
                'identifier' => 'TEST1A',
                'handoff' => 2,
                'initial_altitude' => 5000,
                'prenotes' => [],
                'departure_interval_group' => 3,
            ],
        ];

        $this->assertEquals($expected, $this->service->getSidsDependency());
    }
}
