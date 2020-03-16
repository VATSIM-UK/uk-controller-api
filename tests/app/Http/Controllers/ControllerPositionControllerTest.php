<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;

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
                "frequency" => 118.5,
                "top-down" => [
                    "EGLL",
                ],
            ],
            "EGLL_N_APP" =>[
                "frequency" => 119.72,
                "top-down" => [
                    "EGLL",
                ],
            ],
            "LON_S_CTR" => [
                "frequency" => 129.42,
                "top-down" => [
                    "EGLL",
                ],
            ],
            "LON_C_CTR" => [
                "frequency" => 127.1,
                "top-down" => [
                    "EGBB",
                ],
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'controller-positions')
            ->assertJson($expected);
    }
}
