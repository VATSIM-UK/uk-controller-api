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
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'hold')->seeStatusCode(200);
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

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'hold')->seeJsonEquals($expected);
    }

    public function testItReturns200OnGenericHoldProfileSuccess()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'hold/profile')->seeStatusCode(200);
    }

    public function testItReturnsGenericHoldProfiles()
    {
        $expected = [
            [
                'id' => 1,
                'name' => 'Generic Hold Profile',
                'holds' => [1]
            ]
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'hold/profile')->seeJsonEquals($expected);
    }

    public function testItReturns200OnUserHoldProfileSuccess()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'hold/profile/user')->seeStatusCode(200);
    }

    public function testItReturnsUserHoldProfiles()
    {
        $expected = [
            [
                'id' => 2,
                'name' => 'User Hold Profile',
                'holds' => [1, 2]
            ]
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'hold/profile/user')->seeJsonEquals($expected);
    }

    public function testDeleteUserHoldProfilesReturnsNoContent()
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, 'hold/profile/user/2')->seeStatusCode(204);
    }

    public function testDeleteUserHoldProfilesDeletesTheHoldProfile()
    {
        $this->seeInDatabase(
            'hold_profile',
            [
                'id' => 2,
            ]
        );
        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, 'hold/profile/user/2');
        $this->notSeeInDatabase(
            'hold_profile',
            [
                'id' => 2,
            ]
        );
    }

    public function testCreateUserHoldProfileReturns400NoName()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile/user',
            [
                'holds' => [1, 2],
            ]
        )->seeStatusCode(400);
    }

    public function testCreateUserHoldProfileReturns400NameNotAString()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile/user',
            [
                'name' => 123,
                'holds' => [1, 2],
            ]
        )->seeStatusCode(400);
    }

    public function testCreateUserHoldProfileReturns400NoHolds()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile/user',
            [
                'name' => 'Newly Created Profile',
            ]
        )->seeStatusCode(400);
    }

    public function testCreateUserHoldProfileReturns400HoldsNotAnArray()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile/user',
            [
                'name' => 'Newly Created Profile',
                'holds' => 123,
            ]
        )->seeStatusCode(400);
    }

    public function testCreateUserHoldProfileReturns400HoldsNonNumeric()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile/user',
            [
                'name' => 'Newly Created Profile',
                'holds' => ['abc', 1, 2, 3],
            ]
        )->seeStatusCode(400);
    }

    public function testCreateUserHoldProfileReturnsResponse()
    {
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile/user',
            [
                'name' => 'Newly Created Profile',
                'holds' => [1, 2],
            ]
        )
            ->seeJson(['id' => HoldProfile::orderBy('id', 'DESC')->get()->first()->id])
            ->seeStatusCode(201);
    }

    public function testCreateUserHoldCreatesProfile()
    {
        Carbon::setTestNow(Carbon::now());
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'hold/profile/user',
            [
                'name' => 'Newly Created Profile',
                'holds' => [1, 2],
            ]
        );

        $profileId = json_decode($this->response->getContent(), true)['id'];
        $this->seeInDatabase(
            'hold_profile',
            [
                'id' => $profileId,
                'name' => 'Newly Created Profile',
                'user_id' => self::ACTIVE_USER_CID,
                'created_at' => Carbon::now(),
            ]
        );

        $this->seeInDatabase(
            'hold_profile_hold',
            [
                'hold_profile_id' => $profileId,
                'hold_id' => 1,
            ]
        );

        $this->seeInDatabase(
            'hold_profile_hold',
            [
                'hold_profile_id' => $profileId,
                'hold_id' => 2,
            ]
        );
    }
}
