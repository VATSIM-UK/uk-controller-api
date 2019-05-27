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

        $response->seeJson($expected)->seeStatusCode(200);
    }

    public function testItReturnsAllTmaMinStacks()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'msl/tma');
        $expected = [
            'MTMA' => 6000,
        ];

        $response->seeJson($expected)->seeStatusCode(200);
    }

    public function testItReturnsMinStackForAirfield()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'msl/airfield/EGLL');
        $expected = [
            'msl' => 7000,
        ];

        $response->seeJson($expected)->seeStatusCode(200);
    }

    public function testItReturns404IfAirfieldMslNotFound()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'msl/airfield/EGKK');
        $response->seeStatusCode(404);
    }

    public function testItReturnsMinStackForTma()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'msl/tma/MTMA');
        $expected = [
            'msl' => 6000,
        ];

        $response->seeJson($expected)->seeStatusCode(200);
    }

    public function testItReturns404IfTmaMslNotFound()
    {
        $response = $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'msl/tma/STMA');
        $response->seeStatusCode(404);
    }
}
