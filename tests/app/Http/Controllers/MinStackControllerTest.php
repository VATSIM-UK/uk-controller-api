<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;

class MinStackControllerTest extends BaseApiTestCase
{
    public function testItReturnsAllAirfieldMinStacks()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'msl/airfield');
        $expected = [
            'EGLL' => 7000,
        ];

        $response->assertJson($expected)->assertStatus(200);
    }

    public function testItReturnsAllTmaMinStacks()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'msl/tma');
        $expected = [
            'MTMA' => 6000,
        ];

        $response->assertJson($expected)->assertStatus(200);
    }

    public function testItReturnsAllMinStacks()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'msl');
        $expected = [
            'EGLL' => 7000,
            'MTMA' => 6000,
        ];

        $response->assertJson($expected)->assertStatus(200);
    }

    public function testItReturnsMinStackForAirfield()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'msl/airfield/EGLL');
        $expected = [
            'msl' => 7000,
        ];

        $response->assertJson($expected)->assertStatus(200);
    }

    public function testItReturns404IfAirfieldMslNotFound()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'msl/airfield/EGKK');
        $response->assertStatus(404);
    }

    public function testItReturnsMinStackForTma()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'msl/tma/MTMA');
        $expected = [
            'msl' => 6000,
        ];

        $response->assertJson($expected)->assertStatus(200);
    }

    public function testItReturns404IfTmaMslNotFound()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'msl/tma/STMA');
        $response->assertStatus(404);
    }
}
