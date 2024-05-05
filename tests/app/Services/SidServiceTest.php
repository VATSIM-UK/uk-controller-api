<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Sid;

class SidServiceTest extends BaseFunctionalTestCase
{
    private readonly SidService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(SidService::class);
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
