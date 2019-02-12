<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Hold\Hold;
use App\Models\User\User;
use Illuminate\Support\Facades\Cache;

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
    Cache::shouldReceive('has')
        ->with(HoldService::CACHE_KEY)
        ->andReturn(false);

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


    Cache::shouldReceive('has')
        ->with(HoldService::CACHE_KEY)
        ->andReturn(false);

    Cache::shouldReceive('forever')
        ->with(HoldService::CACHE_KEY, $expected)
        ->andReturn(true);
    $this->holdService->getHolds();
}

    public function testItReturnsCachedHolds()
    {
        Cache::shouldReceive('has')
            ->with(HoldService::CACHE_KEY)
            ->andReturn(true);

        Cache::shouldReceive('get')
            ->with(HoldService::CACHE_KEY)
            ->andReturn(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $this->holdService->getHolds());
    }

    public function testCacheClearClearsTheHoldCache()
    {
        Cache::shouldReceive('forget')
            ->with(HoldService::CACHE_KEY);
        $this->holdService->clearCache();
    }

    public function testGetGenericHoldProfilesReturnsGenericProfilesWithHoldIds()
    {
        $expected = [
            [
                'id' => 1,
                'name' => 'Generic Hold Profile',
                'holds' => [1],
            ]
        ];
        $this->assertEquals($expected, $this->holdService->getGenericHoldProfiles());
    }

    public function testGetUserHoldProfilesReturnsUserProfilesWithHoldIds()
    {
        $this->actingAs(User::find(self::ACTIVE_USER_CID));
        $expected = [
            [
                'id' => 2,
                'name' => 'User Hold Profile',
                'holds' => [1, 2],
            ]
        ];
        $this->assertEquals($expected, $this->holdService->getUserHoldProfiles());
    }

    public function testGetUserAndGenericHoldProfilesReturnsUserAndGenericProfiles()
    {
        $this->actingAs(User::find(self::ACTIVE_USER_CID));
        $expected = [
            [
                'id' => 1,
                'name' => 'Generic Hold Profile',
                'holds' => [1],
            ],
            [
                'id' => 2,
                'name' => 'User Hold Profile',
                'holds' => [1, 2],
            ]
        ];
        $this->assertEquals($expected, $this->holdService->getUserAndGenericHoldProfiles());
    }
}
