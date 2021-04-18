<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Controller\ControllerPosition;

class ControllerPositionControllerTest extends BaseApiTestCase
{
    public function testItReturnsControllerPositions()
    {
        $expected = [
            [
                "id" => 1,
                "callsign" => "EGLL_S_TWR",
                "frequency" => 118.5,
            ],
            [
                "id" => 2,
                "callsign" => "EGLL_N_APP",
                "frequency" => 119.72,
            ],
            [
                "id" => 3,
                "callsign" => "LON_S_CTR",
                "frequency" => 129.42,
            ],
            [
                "id" => 4,
                "callsign" => "LON_C_CTR",
                "frequency" => 127.1,
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'controller')
            ->assertJson($expected);
    }

    public function testItReturnsControllerPositionsDependency()
    {
        $expected = [
            "EGLL_S_TWR" => [
                'id' => 1,
                "frequency" => 118.5,
                "top-down" => [
                    "EGLL",
                ],
                'requests_departure_releases' => true,
                'receives_departure_releases' => false,
            ],
            "EGLL_N_APP" =>[
                'id' => 2,
                "frequency" => 119.72,
                "top-down" => [
                    "EGLL",
                ],
                'requests_departure_releases' => true,
                'receives_departure_releases' => true,
            ],
            "LON_S_CTR" => [
                'id' => 3,
                "frequency" => 129.42,
                "top-down" => [
                    "EGLL",
                ],
                'requests_departure_releases' => true,
                'receives_departure_releases' => true,
            ],
            "LON_C_CTR" => [
                'id' => 4,
                "frequency" => 127.1,
                "top-down" => [
                    "EGBB",
                ],
                'requests_departure_releases' => false,
                'receives_departure_releases' => false,
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'controller-positions')
            ->assertJson($expected);
    }
}
