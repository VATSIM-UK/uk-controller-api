<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Airfield\Airfield;
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

    public function testItReturnsTopDownPositions()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'controller/LON_S_CTR/top-down')->assertExactJson([
            [
                'id' => 2,
                'callsign' => 'EGLL_N_APP',
                'description' => null,
                'frequency' => '119.725',
                'requests_departure_releases' => true,
                'receives_departure_releases' => true,
                'sends_prenotes' => true,
                'receives_prenotes' => true,
            ],
            [
                'id' => 1,
                'callsign' => 'EGLL_S_TWR',
                'description' => null,
                'frequency' => '118.500',
                'requests_departure_releases' => true,
                'receives_departure_releases' => false,
                'sends_prenotes' => true,
                'receives_prenotes' => false,
            ],
        ]);
    }

    public function testItReturnsEmptyTopDownForBottomPosition()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'controller/EGLL_S_TWR/top-down')->assertExactJson([]);
    }

    public function testItReturnsNotFoundForUnknownTopDownPosition()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'controller/XXX_CTR/top-down')
            ->assertNotFound()
            ->assertJson(['message' => 'Controller position not found']);
    }

    public function testItDeduplicatesTopDownPositionsAcrossAirfields()
    {
        Airfield::fromCode('EGKR')->controllers()->attach([
            ['controller_position_id' => 3, 'order' => 3],
            ['controller_position_id' => 2, 'order' => 2],
            ['controller_position_id' => 1, 'order' => 1],
        ]);

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'controller/LON_S_CTR/top-down')->assertExactJson([
            [
                'id' => 2,
                'callsign' => 'EGLL_N_APP',
                'description' => null,
                'frequency' => '119.725',
                'requests_departure_releases' => true,
                'receives_departure_releases' => true,
                'sends_prenotes' => true,
                'receives_prenotes' => true,
            ],
            [
                'id' => 1,
                'callsign' => 'EGLL_S_TWR',
                'description' => null,
                'frequency' => '118.500',
                'requests_departure_releases' => true,
                'receives_departure_releases' => false,
                'sends_prenotes' => true,
                'receives_prenotes' => false,
            ],
        ]);
    }
}
