<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Services\ControllerService;

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
                "frequency" => 119.725,
            ],
            [
                "id" => 3,
                "callsign" => "LON_S_CTR",
                "frequency" => 129.425,
            ],
            [
                "id" => 4,
                "callsign" => "LON_C_CTR",
                "frequency" => 127.1,
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'controller')->assertJson($expected);
    }

    public function testItControllerPositionsDependency()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'controller-positions-v2')->assertExactJson(
                $this->app->make(ControllerService::class)->getControllerPositionsDependency()->toArray()
            );
    }
}
