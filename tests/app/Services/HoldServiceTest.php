<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Hold\Hold;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
                'user_profile' => false,
            ],
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
                'user_profile' => true,
            ],
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
                'user_profile' => false,
            ],
            [
                'id' => 2,
                'name' => 'User Hold Profile',
                'holds' => [1, 2],
                'user_profile' => true,
            ],
        ];
        $this->assertEquals($expected, $this->holdService->getUserAndGenericHoldProfiles());
    }

    public function testItDeletesHoldProfiles()
    {
        $this->actingAs(User::find(self::ACTIVE_USER_CID));
        $this->seeInDatabase(
            'hold_profile',
            [
                'id' => 2,
                'user_id' => self::ACTIVE_USER_CID,
            ]
        );

        $this->holdService->deleteUserHoldProfile(2);

        $this->notSeeInDatabase(
            'hold_profile',
            [
                'id' => 2,
                'user_id' => self::ACTIVE_USER_CID,
            ]
        );
    }

    public function testItDoesntDeleteNonUserProfiles()
    {
        $this->actingAs(User::find(self::ACTIVE_USER_CID));
        $this->seeInDatabase(
            'hold_profile',
            [
                'id' => 1,
                'user_id' => null,
            ]
        );

        $this->holdService->deleteUserHoldProfile(1);

        $this->seeInDatabase(
            'hold_profile',
            [
                'id' => 1,
                'user_id' => null,
            ]
        );
    }

    public function testItDeletesRelatedHolds()
    {
        $this->actingAs(User::find(self::ACTIVE_USER_CID));
        $this->seeInDatabase(
            'hold_profile_hold',
            [
                'hold_profile_id' => 2,
                'hold_id' => 2,
            ]
        );

        $this->holdService->deleteUserHoldProfile(2);

        $this->notSeeInDatabase(
            'hold_profile_hold',
            [
                'hold_profile_id' => 2,
                'hold_id' => 2,
            ]
        );
    }

    public function testItCreatesUserHoldProfiles()
    {
        $this->actingAs(User::find(self::ACTIVE_USER_CID));
        Carbon::setTestNow(Carbon::now());
        $holdProfile = $this->holdService->createUserHoldProfile('New User Profile', [1, 2]);

        $this->seeInDatabase(
            'hold_profile',
            [
                'id' => $holdProfile->id,
                'user_id' => self::ACTIVE_USER_CID,
                'name' => 'New User Profile',
                'created_at' => Carbon::now()->toDateTimeString(),
            ]
        );
    }

    public function testItAddsHoldsToNewProfiles()
    {
        $this->actingAs(User::find(self::ACTIVE_USER_CID));
        Carbon::setTestNow(Carbon::now());
        $holdProfile = $this->holdService->createUserHoldProfile('New User Profile', [1, 2]);

        $this->seeInDatabase(
            'hold_profile_hold',
            [
                'hold_profile_id' => $holdProfile->id,
                'hold_id' => 1,
            ]
        );

        $this->seeInDatabase(
            'hold_profile_hold',
            [
                'hold_profile_id' => $holdProfile->id,
                'hold_id' => 2,
            ]
        );
    }

    public function testItUpdatesHoldProfiles()
    {
        $this->actingAs(User::find(self::ACTIVE_USER_CID));
        Carbon::setTestNow(Carbon::now());
        $this->holdService->updateUserHoldProfile(2, 'Super New User Profile', [2]);

        $this->seeInDatabase(
            'hold_profile',
            [
                'id' => 2,
                'user_id' => self::ACTIVE_USER_CID,
                'name' => 'Super New User Profile',
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]
        );
    }

    public function testItUpdatesHoldsForUserProfiles()
    {
        $this->actingAs(User::find(self::ACTIVE_USER_CID));
        Carbon::setTestNow(Carbon::now());
        $this->holdService->updateUserHoldProfile(2, 'Super New User Profile', [2]);

        $this->notSeeInDatabase(
            'hold_profile_hold',
            [
                'hold_profile_id' => 2,
                'hold_id' => 1,
            ]
        );

        $this->seeInDatabase(
            'hold_profile_hold',
            [
                'hold_profile_id' => 2,
                'hold_id' => 2,
            ]
        );
    }

    public function testItDoesntUpdateOtherUsersHoldProfiles()
    {
        $this->actingAs(User::find(self::BANNED_USER_CID));
        Carbon::setTestNow(Carbon::now());
        $this->expectException(ModelNotFoundException::class);
        $this->holdService->updateUserHoldProfile(2, 'Super New User Profile', [2]);
    }
}
