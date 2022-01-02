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

    public function testItGetsASid()
    {
        $expected = [
            'id' => 1,
            'identifier' => 'TEST1X',
            'runway_id' => 1,
            'handoff_id' => 1,
            'initial_altitude' => 3000,
            'initial_heading' => null,
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
        Sid::where('identifier', 'TEST1Y')->update(['initial_heading' => 255]);
        $expected = [
            [
                'id' => 1,
                'identifier' => 'TEST1X',
                'runway_id' => 1,
                'handoff_id' => 1,
                'initial_altitude' => 3000,
                'initial_heading' => null,
                'prenotes' => [
                    1,
                ],
                'sid_departure_interval_group_id' => null,
            ],
            [
                'id' => 2,
                'identifier' => 'TEST1Y',
                'runway_id' => 2,
                'handoff_id' => 1,
                'initial_altitude' => 4000,
                'initial_heading' => 255,
                'prenotes' => [],
                'sid_departure_interval_group_id' => null,
            ],
            [
                'id' => 3,
                'identifier' => 'TEST1A',
                'runway_id' => 3,
                'handoff_id' => 2,
                'initial_altitude' => 5000,
                'initial_heading' => null,
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
        $this->assertDatabaseHas('sid', ['identifier' => 'TEST1M', 'initial_altitude' => 3000, 'runway_id' => 1]);
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
                'runway_id' => 2,
                'sid_departure_interval_group_id' => null,
            ]
        );
    }

    public function testItGetsSidsDependency()
    {
        Sid::find(1)->update(['sid_departure_interval_group_id' => 1]);
        Sid::find(2)->update(['sid_departure_interval_group_id' => 2]);
        Sid::find(3)->update(['sid_departure_interval_group_id' => 3]);
        Sid::find(2)->update(['initial_heading' => 255]);
        $expected = [
            [
                'id' => 1,
                'identifier' => 'TEST1X',
                'airfield' => 'EGLL',
                'runway_id' => 1,
                'handoff' => 1,
                'initial_altitude' => 3000,
                'initial_heading' => null,
                'departure_interval_group' => 1,
                'prenotes' => [
                    1,
                ],
            ],
            [
                'id' => 2,
                'airfield' => 'EGLL',
                'identifier' => 'TEST1Y',
                'runway_id' => 2,
                'handoff' => 1,
                'initial_altitude' => 4000,
                'initial_heading' => 255,
                'prenotes' => [],
                'departure_interval_group' => 2,
            ],
            [
                'id' => 3,
                'airfield' => 'EGBB',
                'identifier' => 'TEST1A',
                'runway_id' => 3,
                'handoff' => 2,
                'initial_altitude' => 5000,
                'initial_heading' => null,
                'prenotes' => [],
                'departure_interval_group' => 3,
            ],
        ];

        $this->assertEquals($expected, $this->service->getSidsDependency());
    }
}
