<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use Illuminate\Support\Facades\DB;

class StandControllerTest extends BaseApiTestCase
{
    public function testItReturnsStandDependency()
    {
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

    public function testItReturnsAllStandAssignments()
    {
        StandAssignment::insert(
            [
                [
                    'callsign' => 'BAW123',
                    'stand_id' => 1,
                ],
                [
                    'callsign' => 'BAW456',
                    'stand_id' => 2,
                ],
            ]
        );

        $expected = [
            'BAW123' => 1,
            'BAW456' => 2
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'stand/assignment')
            ->assertJson($expected)
            ->assertStatus(200);
    }
}
