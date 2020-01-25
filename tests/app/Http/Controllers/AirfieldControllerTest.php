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
                'controllers' => [
                    4,
                ],
                'pairing-prenotes' => [],
            ],
            [
                'id' => 3,
                'code' => 'EGKR',
                'transition_altitude' => 6000,
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
}
