<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Departure\SidDepartureIntervalGroup;
use Carbon\Carbon;

class DepartureServiceTest extends BaseFunctionalTestCase
{
    private DepartureService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(DepartureService::class);
        Carbon::setTestNow(Carbon::now()->startOfSecond());
    }

    public function testItReturnsDepartureIntervalGroups()
    {
        SidDepartureIntervalGroup::find(1)->relatedGroups()->sync(
            [1 => ['interval' => 25], 2 => ['interval' => 73]],
        );

        SidDepartureIntervalGroup::find(2)->relatedGroups()->sync(
            [1 => ['interval' => 26], 2 => ['interval' => 52]],
        );

        SidDepartureIntervalGroup::find(3)->relatedGroups()->sync(
            [3 => ['interval' => 99, 'apply_speed_groups' => false]]
        );

        $expected = [
            [
                'id' => 1,
                'key' => 'GROUP_ONE',
                'related_groups' => [
                    [
                        'id' => 1,
                        'interval' => 25,
                        'apply_speed_groups' => true,
                    ],
                    [
                        'id' => 2,
                        'interval' => 73,
                        'apply_speed_groups' => true,
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
                        'apply_speed_groups' => true,
                    ],
                    [
                        'id' => 2,
                        'interval' => 52,
                        'apply_speed_groups' => true,
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
                        'apply_speed_groups' => false,
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $this->service->getDepartureIntervalGroupsDependency());
    }
}
