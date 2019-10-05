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
}
