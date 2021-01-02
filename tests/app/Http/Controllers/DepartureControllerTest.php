<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Aircraft\WakeCategory;
use App\Models\Departure\DepartureInterval;
use App\Models\Departure\DepartureIntervalType;
use App\Models\Departure\SidDepartureIntervalGroup;
use App\Models\Sid;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DepartureControllerTest extends BaseApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
        $this->withoutEvents();
    }

    public function testItReturnsActiveIntervals()
    {
        $interval1 = DepartureInterval::create(
            [
                'interval' => 2,
                'type_id' => DepartureIntervalType::where('key', 'mdi')->first()->id,
                'expires_at' => Carbon::now()->addSecond()
            ]
        );
        $interval1->sids()->attach([1, 2]);

        $interval2 = DepartureInterval::create(
            [
                'interval' => 4,
                'type_id' => DepartureIntervalType::where('key', 'mdi')->first()->id,
                'expires_at' => Carbon::now()->addSecond()
            ]
        );

        $interval2->sids()->attach([3]);

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'departure/intervals')
            ->assertOk()
            ->assertJson([$interval1->toArray(), $interval2->toArray()]);
    }

    public function testItExpiresIntervals()
    {
        $interval = DepartureInterval::create(
            [
                'interval' => 2,
                'type_id' => DepartureIntervalType::where('key', 'mdi')->first()->id,
                'expires_at' => Carbon::now()->addHour()
            ]
        );

        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, sprintf('departure/interval/%d', $interval->id))
            ->assertNoContent();

        $this->assertDatabaseHas(
            'departure_intervals',
            [
                'id' => $interval->id,
                'expires_at' => Carbon::now()
            ]
        );
    }

    public function badIntervalCreationProvider(): array
    {
        return [
            'Missing type' => [
                [
                    'interval' => 5,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'EGLL',
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Invalid type' => [
                [
                    'type' => 'abc',
                    'interval' => 5,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'EGLL',
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Missing interval' => [
                [
                    'type' => 'mdi',
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'EGLL',
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Invalid interval' => [
                [
                    'type' => 'mdi',
                    'interval' => 'abc',
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'EGLL',
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Interval too small' => [
                [
                    'type' => 'mdi',
                    'interval' => 0,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'EGLL',
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Invalid airfield' => [
                [
                    'type' => 'mdi',
                    'interval' => 5,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'abc',
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Invalid airfield type' => [
                [
                    'type' => 'mdi',
                    'interval' => 5,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 123,
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Missing airfield' => [
                [
                    'type' => 'mdi',
                    'interval' => 5,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Missing sids' => [
                [
                    'type' => 'mdi',
                    'interval' => 5,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'EGLL',
                ]
            ],
            'Invalid sids' => [
                [
                    'type' => 'mdi',
                    'interval' => 5,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'EGLL',
                    'sids' => 123
                ]
            ],
            'Sids empty' => [
                [
                    'type' => 'mdi',
                    'interval' => 5,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'EGLL',
                    'sids' => []
                ]
            ],
            'Invalid sids types' => [
                [
                    'type' => 'mdi',
                    'interval' => 5,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'EGLL',
                    'sids' => [
                        'TEST1X',
                        123
                    ]
                ]
            ],
            'Missing expires at' => [
                [
                    'type' => 'mdi',
                    'interval' => 5,
                    'airfield' => 'EGLL',
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Invalid expires at' => [
                [
                    'type' => 'mdi',
                    'interval' => 5,
                    'expires_at' => 'abc',
                    'airfield' => 'EGLL',
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Expires at too early' => [
                [
                    'type' => 'mdi',
                    'interval' => 5,
                    'expires_at' => Carbon::now()->subSecond(),
                    'airfield' => 'EGLL',
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
        ];
    }

    /**
     * @dataProvider badIntervalCreationProvider
     */
    public function testItReturnsBadRequestOnBadIntervalCreationData(array $data)
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'departure/intervals', $data)
            ->assertStatus(400);
    }

    public function testItCreatesMinimumDepartureInterval()
    {
        $previousInterval = DepartureInterval::create(
            [
                'interval' => 2,
                'type_id' => DepartureIntervalType::where('key', 'mdi')->first()->id,
                'expires_at' => Carbon::now()->addSecond()
            ]
        );

        $data = [
            'type' => 'mdi',
            'interval' => 5,
            'expires_at' => Carbon::now()->addMinutes(15),
            'airfield' => 'EGLL',
            'sids' => [
                'TEST1X',
                'TEST1Y'
            ]
        ];

        $expected = [
            'id' => $previousInterval->id + 1,
            'type' => 'mdi',
            'interval' => 5,
            'expires_at' => Carbon::now()->addMinutes(15),
            'sids' => [
                'EGLL' => [
                    'TEST1X',
                    'TEST1Y',
                ],
            ],
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'departure/intervals', $data)
            ->assertStatus(201)
            ->assertJson($expected);
    }

    public function testItCreatesAverageDepartureInterval()
    {
        $previousInterval = DepartureInterval::create(
            [
                'interval' => 2,
                'type_id' => DepartureIntervalType::where('key', 'mdi')->first()->id,
                'expires_at' => Carbon::now()->addSecond()
            ]
        );

        $data = [
            'type' => 'adi',
            'interval' => 5,
            'expires_at' => Carbon::now()->addMinutes(15),
            'airfield' => 'EGLL',
            'sids' => [
                'TEST1X',
                'TEST1Y'
            ]
        ];

        $expected = [
            'id' => $previousInterval->id + 1,
            'type' => 'adi',
            'interval' => 5,
            'expires_at' => Carbon::now()->addMinutes(15),
            'sids' => [
                'EGLL' => [
                    'TEST1X',
                    'TEST1Y',
                ],
            ],
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'departure/intervals', $data)
            ->assertStatus(201)
            ->assertJson($expected);
    }

    public function badIntervalUpdateProvider(): array
    {
        return [
            'Missing interval' => [
                [
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'EGLL',
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Invalid interval' => [
                [
                    'interval' => 'abc',
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'EGLL',
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Interval too small' => [
                [
                    'interval' => 0,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'EGLL',
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Invalid airfield' => [
                [
                    'interval' => 5,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'abc',
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Invalid airfield type' => [
                [
                    'interval' => 5,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 123,
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Missing airfield' => [
                [
                    'interval' => 5,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Missing sids' => [
                [
                    'interval' => 5,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'EGLL',
                ]
            ],
            'Invalid sids' => [
                [
                    'interval' => 5,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'EGLL',
                    'sids' => 123
                ]
            ],
            'Sids empty' => [
                [
                    'interval' => 5,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'EGLL',
                    'sids' => []
                ]
            ],
            'Invalid sids types' => [
                [
                    'interval' => 5,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'airfield' => 'EGLL',
                    'sids' => [
                        'TEST1X',
                        123
                    ]
                ]
            ],
            'Missing expires at' => [
                [
                    'interval' => 5,
                    'airfield' => 'EGLL',
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Invalid expires at' => [
                [
                    'interval' => 5,
                    'expires_at' => 'abc',
                    'airfield' => 'EGLL',
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
            'Expires at too early' => [
                [
                    'interval' => 5,
                    'expires_at' => Carbon::now()->subSecond(),
                    'airfield' => 'EGLL',
                    'sids' => [
                        'TEST1X',
                        'TEST1Y'
                    ]
                ]
            ],
        ];
    }

    /**
     * @dataProvider badIntervalUpdateProvider
     */
    public function testItReturnsBadRequestOnBadIntervalUpdateData(array $data)
    {
        $interval = DepartureInterval::create(
            [
                'interval' => 2,
                'type_id' => DepartureIntervalType::where('key', 'mdi')->first()->id,
                'expires_at' => Carbon::now()->addSecond()
            ]
        );
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, sprintf('departure/interval/%d', $interval->id), $data)
            ->assertStatus(400);
    }

    public function testItReturnsNotFoundOnUnknownInterval()
    {
        $data = [
            'interval' => 5,
            'expires_at' => Carbon::now()->addMinutes(15),
            'airfield' => 'EGLL',
            'sids' => [
                'TEST1X',
                'TEST1Y'
            ]
        ];
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'departure/interval/999', $data)
            ->assertNotFound();
    }

    public function testItUpdatesAnInterval()
    {
        $interval = DepartureInterval::create(
            [
                'interval' => 2,
                'type_id' => DepartureIntervalType::where('key', 'mdi')->first()->id,
                'expires_at' => Carbon::now()->addSecond()
            ]
        );
        $interval->sids()->attach([1, 2]);

        $data = [
            'type' => 'mdi',
            'interval' => 5,
            'expires_at' => Carbon::now()->addMinutes(15),
            'airfield' => 'EGBB',
            'sids' => [
                'TEST1A'
            ]
        ];

        $expected = [
            'id' => $interval->id,
            'type' => 'mdi',
            'interval' => 5,
            'expires_at' => Carbon::now()->addMinutes(15),
            'sids' => [
                'EGBB' => [
                    'TEST1A',
                ],
            ],
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, sprintf('departure/interval/%d', $interval->id), $data)
            ->assertOk()
            ->assertJson($expected);
    }

    public function testItReturnsWakeIntervalsDependency()
    {
        DB::table('departure_wake_intervals')->delete();

        WakeCategory::where('code', 'H')->first()->departureIntervals()->sync(
            [
                WakeCategory::where('code', 'S')->first()->id => [
                    'interval' => 1,
                    'intermediate' => false,
                ],
                WakeCategory::where('code', 'H')->first()->id => [
                    'interval' => 2,
                    'intermediate' => true,
                ],
            ]
        );

        WakeCategory::where('code', 'LM')->first()->departureIntervals()->sync(
            [
                WakeCategory::where('code', 'L')->first()->id => [
                    'interval' => 3,
                    'intermediate' => false,
                ],
            ]
        );

        $expected = [
            [
                'lead' => 'LM',
                'follow' => 'L',
                'interval' => 3,
                'intermediate' => false,
            ],
            [
                'lead' => 'H',
                'follow' => 'S',
                'interval' => 1,
                'intermediate' => false,
            ],
            [
                'lead' => 'H',
                'follow' => 'H',
                'interval' => 2,
                'intermediate' => true,
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'departure/intervals/wake/dependency')
            ->assertOk()
            ->assertJson($expected);
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
