<?php

namespace App\Services;

use App\BaseFunctionalTestCase;

class VrpServiceTest extends BaseFunctionalTestCase
{
    private VrpService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(VrpService::class);
    }

    public function testItReturnsDependency()
    {
        $expected = [
            [
                'id' => 1,
                'name' => 'VRP One',
                'short_name' => 'V1',
                'latitude' => 1.0,
                'longitude' => 2.0,
                'airfields' => [1, 2],
            ],
            [
                'id' => 2,
                'name' => 'VRP Two',
                'short_name' => 'V2',
                'latitude' => 3.0,
                'longitude' => 4.0,
                'airfields' => [],
            ]
        ];

        $this->assertEquals($expected, $this->service->getVrpDependency());
    }
}
