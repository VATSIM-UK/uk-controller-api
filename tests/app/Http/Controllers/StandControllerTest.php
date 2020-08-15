<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Stand\Stand;
use Illuminate\Support\Facades\DB;

class StandControllerTest extends BaseApiTestCase
{
    public function testItReturnsStandDependency()
    {
        DB::table('stands')->delete();
        Stand::insert(
            [
                [
                    'airfield_id' => 1,
                    'identifier' => '1L',
                    'latitude' => 'abc',
                    'longitude' => 'def',
                ],
                [
                    'airfield_id' => 1,
                    'identifier' => '251',
                    'latitude' => 'asd',
                    'longitude' => 'hsd',
                ],
                [
                    'airfield_id' => 2,
                    'identifier' => '32',
                    'latitude' => 'fhg',
                    'longitude' => 'sda',
                ],
            ]
        );

        $firstStand = Stand::all()->first()->id;
        $expected = [
            'EGLL' => [
                [
                    'id' => $firstStand,
                    'identifier' => '1L',
                ],
                [
                    'id' => $firstStand + 1,
                    'identifier' => '251',
                ],
            ],
            'EGBB' => [
                [
                    'id' => $firstStand + 2,
                    'identifier' => '32',
                ]
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'stand/dependency')
            ->assertJson($expected)
            ->assertStatus(200);
    }
}
