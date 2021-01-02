<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Departure\DepartureInterval;
use App\Models\Departure\DepartureIntervalType;
use Carbon\Carbon;

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

        $this->makeAuthenticatedApiRequest(self::METHOD_DELETE, sprintf('departure/intervals/%d', $interval->id))
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
            'expires_at' => Carbon::now()->addMinutes(15)
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
            'expires_at' => Carbon::now()->addMinutes(15)
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'departure/intervals', $data)
            ->assertStatus(201)
            ->assertJson($expected);
    }
}
