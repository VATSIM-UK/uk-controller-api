<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;

class AirfieldControllerTest extends BaseApiTestCase
{
    public function testItReturnsAirfieldsWithoutTopDown()
    {
        $expected = [
            [
                "id" => 1,
                "code" => "EGLL",
                "transition_altitude" => 6000,
            ],
            [
                "id" => 2,
                "code" => "EGBB",
                "transition_altitude" => 6000,
            ],
            [
                "id" => 3,
                "code" => "EGKR",
                "transition_altitude" => 6000,
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'airfield')->assertJson($expected);
    }

    public function testItReturnsAirfieldsWithTopDown()
    {
        $expected = [
            [
                "id" => 1,
                "code" => "EGLL",
                "transition_altitude" => 6000,
                "controllers" => [
                    1,
                    2,
                    3,
                ],
            ],
            [
                "id" => 2,
                "code" => "EGBB",
                "transition_altitude" => 6000,
                "controllers" => [
                    4,
                ],
            ],
            [
                "id" => 3,
                "code" => "EGKR",
                "transition_altitude" => 6000,
                "controllers" => [],
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'airfield?controllers=1')->assertJson($expected);
    }
}
