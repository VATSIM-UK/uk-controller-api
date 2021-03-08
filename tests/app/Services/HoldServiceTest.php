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

    public function setUp(): void
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
        Hold::find(1)->deemedSeparatedHolds()->sync(
            [
                2 => ['vsl_insert_distance' => 5],
                3 => ['vsl_insert_distance' => 7]
            ]
        );
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
                        'foo' => 'bar',
                    ],
                ],
                'deemed_separated_holds' => [
                    [
                        'hold_id' => 2,
                        'vsl_insert_distance' => 5,
                    ],
                    [
                        'hold_id' => 3,
                        'vsl_insert_distance' => 7,
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
                'deemed_separated_holds' => [],
            ],
            [
                'id' => 3,
                'fix' => 'MAY',
                'inbound_heading' => 90,
                'minimum_altitude' => 3000,
                'maximum_altitude' => 5000,
                'turn_direction' => 'right',
                'description' => 'Mayfield Low',
                'restrictions' => [],
                'deemed_separated_holds' => [],
            ],
        ];
        $actual = $this->holdService->getHolds();
        $this->assertEquals($expected, $actual);
    }
}
