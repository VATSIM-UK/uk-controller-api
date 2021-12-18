<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Airfield\Airfield;
use App\Models\Metars\Metar;

class MetarControllerTest extends BaseApiTestCase
{
    public function testItReturnsAllMetars()
    {
        Metar::create(
            [
                'airfield_id' => Airfield::where('code', 'EGBB')->first()->id,
                'raw' => 'FOOOO',
                'parsed' => [
                    'bar' => 'baz'
                ],
            ]
        );
        Metar::create(
            [
                'airfield_id' => Airfield::where('code', 'EGLL')->first()->id,
                'raw' => 'FOOOO2',
                'parsed' => [
                    'bar2' => 'baz2'
                ],
            ]
        );

        $expected = [
            [
                'airfield_id' => Airfield::where('code', 'EGBB')->first()->id,
                'raw' => 'FOOOO',
                'parsed' => [
                    'bar' => 'baz'
                ],
            ],
            [
                'airfield_id' => Airfield::where('code', 'EGLL')->first()->id,
                'raw' => 'FOOOO2',
                'parsed' => [
                    'bar2' => 'baz2'
                ],
            ]
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'metar')
            ->assertOk()
            ->assertExactJson($expected);
    }
}
