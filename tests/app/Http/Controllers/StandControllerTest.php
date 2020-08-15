<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Stand\Stand;

class StandControllerTest extends BaseApiTestCase
{
    public function testItReturnsStandDependency()
    {
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

        $expected = [
            'EGLL' => [
                [
                    'identifier' => '1L',
                    'latitude' => 'abc',
                    'longitude' => 'def',
                ],
                [
                    'identifier' => '251',
                    'latitude' => 'asd',
                    'longitude' => 'hsd',
                ],
            ],
            'EGBB' => [
                [
                    'identifier' => '32',
                    'latitude' => 'fhg',
                    'longitude' => 'sda',
                ]
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'stand/dependency')
            ->assertJson($expected)
            ->assertStatus(200);
    }
}
