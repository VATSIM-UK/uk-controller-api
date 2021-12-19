<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Services\AirfieldService;

class AirfieldControllerTest extends BaseApiTestCase
{
    public function testItReturnsAirfields()
    {
        $expected = [
            [
                'id' => 1,
                'code' => 'EGLL',
                'elevation' => 1,
                'transition_altitude' => 6000,
                'wake_category_scheme_id' => 1,
                'controllers' => [
                    1,
                    2,
                    3,
                ],
                'pairing-prenotes' => [
                    2 => [
                        1,
                    ],
                ],
                'handoff_id' => null,
            ],
            [
                'id' => 2,
                'code' => 'EGBB',
                'elevation' => 1,
                'transition_altitude' => 6000,
                'wake_category_scheme_id' => 1,
                'controllers' => [
                    4,
                ],
                'pairing-prenotes' => [],
                'handoff_id' => null,
            ],
            [
                'id' => 3,
                'code' => 'EGKR',
                'elevation' => 1,
                'transition_altitude' => 6000,
                'wake_category_scheme_id' => 1,
                'controllers' => [],
                'pairing-prenotes' => [],
                'handoff_id' => null,
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'airfield')
            ->assertExactJson($expected)
            ->assertStatus(200);

    }

    public function testItGetsAirfieldDependency()
    {
        $expected = $this->app->make(AirfieldService::class)->getAirfieldsDependency();
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'airfield/dependency')
            ->assertOk()
            ->assertJson($expected);
    }
}
