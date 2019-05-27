<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Hold\HoldProfile;
use Carbon\Carbon;

class HoldControllerTest extends BaseApiTestCase
{
    public function testItConstructs()
    {
        $this->assertInstanceOf(HoldController::class, $this->app->make(HoldController::class));
    }

    public function testItReturns200OnHoldDataSuccess()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'hold')->assertStatus(200);
    }

    public function testItReturnsHoldData()
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
                        'foo' => 'bar',
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
                'fix' => 'MAY',
                'inbound_heading' => 90,
                'minimum_altitude' => 3000,
                'maximum_altitude' => 5000,
                'turn_direction' => 'right',
                'description' => 'Mayfield Low',
                'restrictions' => [],
            ],
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'hold')->assertJsonEquals($expected);
    }

    public function testItReturns200OnGenericHoldProfileSuccess()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'hold/profile')->assertStatus(200);
    }

    public function testItReturns200OnUserHoldProfileSuccess()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'hold/profile')->assertStatus(200);
    }

    public function testItReturnsUserHoldProfiles()
    {
        $expected = [
            [
                'id' => 1,
                'name' => 'User Hold Profile',
                'holds' => [1, 2],
            ]
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'hold/profile')->assertJsonEquals($expected);
    }

    public function testDeleteUserHoldProfilesReturnsNoContent()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, 'hold/profile/1')->assertStatus(204);
    }

    public function testDeleteUserHoldProfilesDeletesTheHoldProfile()
    {
        $this->assertDatabaseHas(
            'hold_profile',
            [
                'id' => 1,
            ]
        );
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, 'hold/profile/1');
        $this->assertDatabaseMissing(
            'hold_profile',
            [
                'id' => 1,
            ]
        );
    }

    public function testCreateUserHoldProfileReturns400NoName()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile',
            [
                'holds' => [1, 2],
            ]
        )->assertStatus(400);
    }

    public function testCreateUserHoldProfileReturns400NameNotAString()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile',
            [
                'name' => 123,
                'holds' => [1, 2],
            ]
        )->assertStatus(400);
    }

    public function testCreateUserHoldProfileReturns400NoHolds()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile',
            [
                'name' => 'Newly Created Profile',
            ]
        )->assertStatus(400);
    }

    public function testCreateUserHoldProfileReturns400HoldsNotAnArray()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile',
            [
                'name' => 'Newly Created Profile',
                'holds' => 123,
            ]
        )->assertStatus(400);
    }

    public function testCreateUserHoldProfileReturns400HoldsNonNumeric()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile',
            [
                'name' => 'Newly Created Profile',
                'holds' => ['abc', 1, 2, 3],
            ]
        )->assertStatus(400);
    }

    public function testCreateUserHoldProfileReturnsResponse()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile',
            [
                'name' => 'Newly Created Profile',
                'holds' => [1, 2],
            ]
        )
            ->assertJson(['id' => HoldProfile::orderBy('id', 'DESC')->get()->first()->id])
            ->assertStatus(201);
    }

    public function testCreateUserHoldCreatesProfile()
    {
        Carbon::setTestNow(Carbon::now());
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile',
            [
                'name' => 'Newly Created Profile',
                'holds' => [1, 2],
            ]
        );

        $profileId = json_decode($this->response->getContent(), true)['id'];
        $this->assertDatabaseHas(
            'hold_profile',
            [
                'id' => $profileId,
                'name' => 'Newly Created Profile',
                'user_id' => self::ACTIVE_USER_CID,
                'created_at' => Carbon::now()->toDateTimeString(),
            ]
        );

        $this->assertDatabaseHas(
            'hold_profile_hold',
            [
                'hold_profile_id' => $profileId,
                'hold_id' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'hold_profile_hold',
            [
                'hold_profile_id' => $profileId,
                'hold_id' => 2,
            ]
        );
    }

    public function testUpdateUserHoldProfileReturns400NoName()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile/1',
            [
                'holds' => [1, 2],
            ]
        )->assertStatus(400);
    }

    public function testUpdateUserHoldProfileReturns400NameNotAString()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile/1',
            [
                'name' => 123,
                'holds' => [1, 2],
            ]
        )->assertStatus(400);
    }

    public function testUpdateUserHoldProfileReturns400NoHolds()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile/1',
            [
                'name' => 'Newly Created Profile',
            ]
        )->assertStatus(400);
    }

    public function testUpdateUserHoldProfileReturns400HoldsNotAnArray()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile/1',
            [
                'name' => 'Newly Created Profile',
                'holds' => 123,
            ]
        )->assertStatus(400);
    }

    public function testUpdateUserHoldProfileReturns400HoldsNonNumeric()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile/1',
            [
                'name' => 'Newly Created Profile',
                'holds' => ['abc', 1, 2, 3],
            ]
        )->assertStatus(400);
    }

    public function testUpdateUserHoldProfileReturnsResponse()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile/1',
            [
                'name' => 'Newly Updated Profile',
                'holds' => [1],
            ]
        )
            ->assertStatus(204);
    }

    public function testUpdateUserHoldUpdatesProfile()
    {
        Carbon::setTestNow(Carbon::now());
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile/1',
            [
                'name' => 'Newly Updated Profile',
                'holds' => [1],
            ]
        );

        $this->assertDatabaseHas(
            'hold_profile',
            [
                'id' => 1,
                'name' => 'Newly Updated Profile',
                'user_id' => self::ACTIVE_USER_CID,
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]
        );

        $this->assertDatabaseHas(
            'hold_profile_hold',
            [
                'hold_profile_id' => 1,
                'hold_id' => 1,
            ]
        );

        $this->assertDatabaseMissing(
            'hold_profile_hold',
            [
                'hold_profile_id' => 1,
                'hold_id' => 2,
            ]
        );
    }
}
