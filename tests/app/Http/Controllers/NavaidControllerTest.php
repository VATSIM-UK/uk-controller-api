<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;

class NavaidControllerTest extends BaseApiTestCase
{
    public function testItReturnsNavaidDependency()
    {
        $expected = [
            [
                'id' => 1,
                'identifier' => 'WILLO',
                    'latitude' => 'N050.59.06.000',
                    'longitude' => 'W000.11.30.000',
            ],
            [
                'id' => 2,
                'identifier' => 'TIMBA',
                    'latitude' => 'N050.56.44.000',
                    'longitude' => 'E000.15.42.000',
            ],
            [
                'id' => 3,
                'identifier' => 'MAY',
                    'latitude' => 'N051.01.01.920',
                    'longitude' => 'E000.06.58.000',
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'navaid/dependency')
            ->assertStatus(200)
            ->assertExactJson($expected);
    }
}
