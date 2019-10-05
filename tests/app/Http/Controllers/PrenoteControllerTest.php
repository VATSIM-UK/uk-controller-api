<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;

class PrenoteControllerTest extends BaseApiTestCase
{
    public function testItReturnsPrenoteData()
    {
        $expected = [
            [
                'id' => 1,
                'key' => 'PRENOTE_ONE',
                'description' => 'Prenote One',
                'controllers' => [
                    1,
                    2
                ]
            ],
            [
                'id' => 2,
                'key' => 'PRENOTE_TWO',
                'description' => 'Prenote Two',
                'controllers' => [
                    2,
                    3
                ]
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'prenote')
            ->assertStatus(200)
            ->assertExactJson($expected);
    }
}
