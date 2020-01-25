<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;

class MinStackControllerTest extends BaseApiTestCase
{
    public function testItReturnsAllAirfieldMinStacks()
    {
        $response = $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'msl/airfield');
        $expected = [
            'EGLL' => 7000,
        ];

        $response->assertJson($expected)->assertStatus(200);
    }

    public function testItReturnsAllTmaMinStacks()
    {
        $response = $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'msl/tma');
        $expected = [
            'MTMA' => 6000,
        ];

        $response->assertJson($expected)->assertStatus(200);
    }

    public function testItReturnsAllMinStacks()
    {
        $response = $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'msl');
        $expected = [
            'airfield' => [
                'EGLL' => 7000,
            ],
            'tma' => [
                'MTMA' => 6000,
            ],
        ];

        $response->assertJson($expected)->assertStatus(200);
    }

    public function testItReturnsMinStackForAirfield()
    {
        $response = $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'msl/airfield/EGLL');
        $expected = [
            'msl' => 7000,
        ];

        $response->assertJson($expected)->assertStatus(200);
    }

    public function testItReturns404IfAirfieldMslNotFound()
    {
        $response = $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'msl/airfield/EGKK');
        $response->assertStatus(404);
    }

    public function testItReturnsMinStackForTma()
    {
        $response = $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'msl/tma/MTMA');
        $expected = [
            'msl' => 6000,
        ];

        $response->assertJson($expected)->assertStatus(200);
    }

    public function testItReturns404IfTmaMslNotFound()
    {
        $response = $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'msl/tma/STMA');
        $response->assertStatus(404);
    }
}
