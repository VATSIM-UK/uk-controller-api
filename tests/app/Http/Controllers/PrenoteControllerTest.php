<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Services\PrenoteService;

class PrenoteControllerTest extends BaseApiTestCase
{
    public function testItReturnsPrenoteData()
    {
        $expected = [
            [
                'airfield' => 'EGLL',
                'departure' => 'TEST1X',
                'type' => 'sid',
                'recipient' => [
                    'EGLL_S_TWR',
                    'EGLL_N_APP',
                ],
            ],
            [
                'origin' => 'EGLL',
                'destination' => 'EGBB',
                'type' => 'airfieldPairing',
                'flight_rules' => null,
                'recipient' => [
                    'EGLL_S_TWR',
                    'EGLL_N_APP',
                ],
            ]
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'prenote')
            ->assertStatus(200)
            ->assertExactJson($expected);
    }

    public function testItReturnsPrenotesV2Dependency()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'prenote')
            ->assertStatus(200)
            ->assertExactJson($this->app->make(PrenoteService::class)->getPrenotesV2Dependency());
    }
}
