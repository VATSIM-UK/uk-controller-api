<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;

class AirfieldControllerTest extends BaseApiTestCase
{
    public function testItReturnsAirfields()
    {
        $expected = [
            [
                'id' => 1,
                'code' => 'EGLL',
                'transition_altitude' => 6000,
                'departure_wake_separation_scheme_id' => 1,
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
            ],
            [
                'id' => 2,
                'code' => 'EGBB',
                'transition_altitude' => 6000,
                'departure_wake_separation_scheme_id' => 1,
                'controllers' => [
                    4,
                ],
                'pairing-prenotes' => [],
            ],
            [
                'id' => 3,
                'code' => 'EGKR',
                'transition_altitude' => 6000,
                'departure_wake_separation_scheme_id' => 1,
                'controllers' => [],
                'pairing-prenotes' => [],
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'airfield')
            ->assertExactJson($expected)
            ->assertStatus(200);

    }

    public function testItReturnsAirfieldOwnershipDependency()
    {
        $expected = [
            'EGLL' => [
                'EGLL_S_TWR',
                'EGLL_N_APP',
                'LON_S_CTR',
            ],
            'EGBB' => [
                'LON_C_CTR',
            ],
            'EGKR' => []
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'airfield-ownership')
            ->assertExactJson($expected)
            ->assertStatus(200);
    }

    public function testItGetsAirfieldDependency()
    {
        $expected = [
            [
                'id' => 1,
                'identifier' => 'EGLL',
                'departure_wake_scheme' => 1,
            ],
            [
                'id' => 2,
                'identifier' => 'EGBB',
                'departure_wake_scheme' => 1,
            ],
            [
                'id' => 3,
                'identifier' => 'EGKR',
                'departure_wake_scheme' => 1,
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'airfield/dependency')
            ->assertOk()
            ->assertJson($expected);
    }
}
