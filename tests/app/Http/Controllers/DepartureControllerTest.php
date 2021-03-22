<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Departure\SidDepartureIntervalGroup;
use Carbon\Carbon;

class DepartureControllerTest extends BaseApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
    }

    public function testItReturnsSidIntervalGroupsDependency()
    {
        SidDepartureIntervalGroup::find(1)->relatedGroups()->sync(
            [1 => ['interval' => 25], 2 => ['interval' => 73]],
        );

        SidDepartureIntervalGroup::find(2)->relatedGroups()->sync(
            [1 => ['interval' => 26], 2 => ['interval' => 52]],
        );

        SidDepartureIntervalGroup::find(3)->relatedGroups()->sync(
            [3 => ['interval' => 99]]
        );

        $expected = [
            [
                'id' => 1,
                'key' => 'GROUP_ONE',
                'related_groups' => [
                    [
                        'id' => 1,
                        'interval' => 25,
                    ],
                    [
                        'id' => 2,
                        'interval' => 73,
                    ],
                ],
            ],
            [
                'id' => 2,
                'key' => 'GROUP_TWO',
                'related_groups' => [
                    [
                        'id' => 1,
                        'interval' => 26,
                    ],
                    [
                        'id' => 2,
                        'interval' => 52,
                    ],
                ],
            ],
            [
                'id' => 3,
                'key' => 'GROUP_THREE',
                'related_groups' => [
                    [
                        'id' => 3,
                        'interval' => 99,
                    ],
                ],
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'departure/intervals/sid-groups/dependency')
            ->assertOk()
            ->assertJson($expected);
    }
}
