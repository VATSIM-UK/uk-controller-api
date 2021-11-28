<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;

class FlightRulesControllerTest extends BaseApiTestCase
{
    public function testItReturnsFlightRulesDependency()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, '/flight-rules/dependency')
            ->assertStatus(200)
            ->assertExactJson(
                [
                    [
                        'id' => 1,
                        'euroscope_key' => 'V',
                        'description' => 'VFR',
                    ],
                    [
                        'id' => 2,
                        'euroscope_key' => 'I',
                        'description' => 'IFR',
                    ],
                ]
            );
    }
}
