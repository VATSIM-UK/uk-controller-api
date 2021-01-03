<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Aircraft\RecatCategory;
use App\Models\Aircraft\WakeCategory;
use App\Models\Departure\DepartureRestriction;
use App\Models\Departure\DepartureRestrictionType;
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

    public function testItReturnsActiveRestrictions()
    {
        $restriction1 = DepartureRestriction::create(
            [
                'interval' => 2,
                'type_id' => DepartureRestrictionType::where('key', 'mdi')->first()->id,
                'expires_at' => Carbon::now()->addSecond()
            ]
        );
        $restriction1->sids()->attach([1, 2]);

        $restriction2 = DepartureRestriction::create(
            [
                'interval' => 4,
                'type_id' => DepartureRestrictionType::where('key', 'mdi')->first()->id,
                'expires_at' => Carbon::now()->addSecond()
            ]
        );

        $restriction2->sids()->attach([3]);

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'departure/restrictions')
            ->assertOk()
            ->assertJson([$restriction1->toArray(), $restriction2->toArray()]);
    }

    public function testItExpiresRestrictions()
    {
        $restriction = DepartureRestriction::create(
            [
                'interval' => 2,
                'type_id' => DepartureRestrictionType::where('key', 'mdi')->first()->id,
                'expires_at' => Carbon::now()->addHour()
            ]
        );

        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, sprintf('departure/restriction/%d', $restriction->id))
            ->assertNoContent();

        $this->assertDatabaseHas(
            'departure_restrictions',
            [
                'id' => $restriction->id,
                'expires_at' => Carbon::now()
            ]
        );
    }

    public function badRestrictionCreationProvider(): array
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
     * @dataProvider badRestrictionCreationProvider
     */
    public function testItReturnsBadRequestOnBadRestrictionCreationData(array $data)
    {
        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'departure/restrictions', $data)
            ->assertStatus(400);
    }

    public function testItCreatesMinimumDepartureRestriction()
    {
        $previousRestriction = DepartureRestriction::create(
            [
                'interval' => 2,
                'type_id' => DepartureRestrictionType::where('key', 'mdi')->first()->id,
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
            'id' => $previousRestriction->id + 1,
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

        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'departure/restrictions', $data)
            ->assertStatus(201)
            ->assertJson($expected);
    }

    public function testItCreatesAverageDepartureInterval()
    {
        $previousRestriction = DepartureRestriction::create(
            [
                'interval' => 2,
                'type_id' => DepartureRestrictionType::where('key', 'mdi')->first()->id,
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
            'id' => $previousRestriction->id + 1,
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

        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'departure/restrictions', $data)
            ->assertStatus(201)
            ->assertJson($expected);
    }

    public function badRestrictionUpdateProvider(): array
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
     * @dataProvider badRestrictionUpdateProvider
     */
    public function testItReturnsBadRequestOnBadIntervalUpdateData(array $data)
    {
        $restriction = DepartureRestriction::create(
            [
                'interval' => 2,
                'type_id' => DepartureRestrictionType::where('key', 'mdi')->first()->id,
                'expires_at' => Carbon::now()->addSecond()
            ]
        );
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            sprintf('departure/restriction/%d',
                    $restriction->id),
            $data
        )
            ->assertStatus(400);
    }

    public function testItReturnsNotFoundOnUnknownRestriction()
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
        $this->makeAuthenticatedApiRequest(self::METHOD_PUT, 'departure/restriction/999', $data)
            ->assertNotFound();
    }

    public function testItUpdatesAnInterval()
    {
        $restriction = DepartureRestriction::create(
            [
                'interval' => 2,
                'type_id' => DepartureRestrictionType::where('key', 'mdi')->first()->id,
                'expires_at' => Carbon::now()->addSecond()
            ]
        );
        $restriction->sids()->attach([1, 2]);

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
            'id' => $restriction->id,
            'type' => 'mdi',
            'interval' => 5,
            'expires_at' => Carbon::now()->addMinutes(15),
            'sids' => [
                'EGBB' => [
                    'TEST1A',
                ],
            ],
        ];

        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            sprintf('departure/restriction/%d', $restriction->id),
            $data
        )
            ->assertOk()
            ->assertJson($expected);
    }

    public function testItReturnsUkWakeIntervalsDependency()
    {
        DB::table('departure_uk_wake_intervals')->delete();

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

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'departure/intervals/wake-uk/dependency')
            ->assertOk()
            ->assertJson($expected);
    }

    public function testItReturnsRecatWakeIntervalsDependency()
    {
        DB::table('departure_recat_wake_intervals')->delete();

        RecatCategory::where('code', 'A')->first()->departureIntervals()->sync(
            [
                RecatCategory::where('code', 'B')->first()->id => [
                    'interval' => 1,
                ],
                RecatCategory::where('code', 'C')->first()->id => [
                    'interval' => 2,
                ],
            ]
        );

        RecatCategory::where('code', 'C')->first()->departureIntervals()->sync(
            [
                RecatCategory::where('code', 'F')->first()->id => [
                    'interval' => 3,
                ],
            ]
        );

        $expected = [
            [
                'lead' => 'A',
                'follow' => 'B',
                'interval' => 1,
            ],
            [
                'lead' => 'A',
                'follow' => 'C',
                'interval' => 2,
            ],
            [
                'lead' => 'C',
                'follow' => 'F',
                'interval' => 3,
            ],
        ];

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'departure/intervals/wake-recat/dependency')
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
