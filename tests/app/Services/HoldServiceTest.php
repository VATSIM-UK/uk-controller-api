<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Hold\Hold;

class HoldServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var HoldService
     */
    private $holdService;

    public function setUp()
    {
        parent::setUp();
        $this->holdService = $this->app->make(HoldService::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(HoldService::class, $this->holdService);
    }

    public function testItReturnsAllHolds()
    {
        $expected = [
            [
                'id' => 1,
                'fix' => 'WILLO',
                'inbound_heading' => 285,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'left',
                'description' => 'WILLO',
                'restrictions' => [
                    [
                        'id' => 1,
                        'hold_id' => 1,
                        'restriction' => [
                            'foo' => 'bar',
                        ],
                    ],
                ],
            ],
            [
                'id' => 2,
                'fix' => 'TIMBA',
                'inbound_heading' => 309,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'right',
                'description' => 'TIMBA',
                'restrictions' => [],
            ],
        ];
        $actual = $this->holdService->getHolds();
        $this->assertEquals($expected, $actual);
    }

    public function testItCachesHolds()
    {
        $expected = [
            [
                'id' => 1,
                'fix' => 'WILLO',
                'inbound_heading' => 285,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'left',
                'description' => 'WILLO',
                'restrictions' => [
                    [
                        'id' => 1,
                        'hold_id' => 1,
                        'restriction' => [
                            'foo' => 'bar',
                        ],
                    ],
                ],
            ],
            [
                'id' => 2,
                'fix' => 'TIMBA',
                'inbound_heading' => 309,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'right',
                'description' => 'TIMBA',
                'restrictions' => [],
            ],
        ];
        $this->holdService->getHolds();
        Hold::create(
            [
                'fix' => 'TEST',
                'inbound_heading' => 255,
                'minimum_altitude' => 5000,
                'maximum_altitude' => 10000,
                'turn_direction' => 'left',
                'description' => 'This is a test hold',
            ]
        );

        $actual = $this->holdService->getHolds();
        $this->assertEquals($expected, $actual);
    }

    public function cacheClearClearsTheHoldCache()
    {
        $expected = [
            [
                'id' => 1,
                'fix' => 'WILLO',
                'inbound_heading' => 285,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'left',
                'description' => 'WILLO',
                'restrictions' => [
                    [
                        'id' => 1,
                        'hold_id' => 1,
                        'restriction' => [
                            'foo' => 'bar',
                        ],
                    ],
                ],
            ],
            [
                'id' => 2,
                'fix' => 'TIMBA',
                'inbound_heading' => 309,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'right',
                'description' => 'TIMBA',
                'restrictions' => [],
            ],
            [
                'id' => 3,
                'fix' => 'TEST',
                'inbound_heading' => 255,
                'minimum_altitude' => 5000,
                'maximum_altitude' => 10000,
                'turn_direction' => 'left',
                'description' => 'This is a test hold',
                'restrictions' => [],
            ],
        ];
        $this->holdService->getHolds();

        // Create a new hold and clear cache
        Hold::create(
            [
                'fix' => 'TEST',
                'inbound_heading' => 255,
                'minimum_altitude' => 5000,
                'maximum_altitude' => 10000,
                'turn_direction' => 'left',
                'description' => 'This is a test hold',
            ]
        );
        $this->holdService->clearCache();

        $actual = $this->holdService->getHolds();
        $this->assertEquals($expected, $actual);
    }
}
