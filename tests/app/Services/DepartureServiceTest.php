<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\DepartureRestrictionUpdatedEvent;
use App\Models\Aircraft\RecatCategory;
use App\Models\Aircraft\WakeCategory;
use App\Models\Departure\DepartureRestriction;
use App\Models\Departure\SidDepartureIntervalGroup;
use App\Models\Sid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DepartureServiceTest extends BaseFunctionalTestCase
{
    private DepartureService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(DepartureService::class);
        Carbon::setTestNow(Carbon::now()->startOfSecond());
    }

    public function testItCreatesAMinimumDepartureIntervalWithSids()
    {
        $this->expectsEvents(DepartureRestrictionUpdatedEvent::class);
        $restriction = $this->service->createMinimumDepartureInterval(
            4,
            'EGLL',
            ['TEST1X', 'TEST1Y'],
            Carbon::now()->addMinutes(10)
        );

        $this->assertDatabaseHas(
            'departure_restrictions',
            [
                'id' => $restriction->id,
                'type_id' => 1,
                'interval' => 4,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]
        );

        $this->assertDatabaseHas(
            'departure_restriction_sid',
            [
                'departure_restriction_id' => $restriction->id,
                'sid_id' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'departure_restriction_sid',
            [
                'departure_restriction_id' => $restriction->id,
                'sid_id' => 2,
            ]
        );
    }

    public function testItCreatesAnAverageDepartureIntervalWithSids()
    {
        $this->expectsEvents(DepartureRestrictionUpdatedEvent::class);
        $restriction = $this->service->createAverageDepartureInterval(
            4,
            'EGLL',
            ['TEST1X', 'TEST1Y'],
            Carbon::now()->addMinutes(10)
        );

        $this->assertDatabaseHas(
            'departure_restrictions',
            [
                'id' => $restriction->id,
                'type_id' => 2,
                'interval' => 4,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]
        );

        $this->assertDatabaseHas(
            'departure_restriction_sid',
            [
                'departure_restriction_id' => $restriction->id,
                'sid_id' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'departure_restriction_sid',
            [
                'departure_restriction_id' => $restriction->id,
                'sid_id' => 2,
            ]
        );
    }

    public function testItUpdatesRestrictions()
    {
        $this->expectsEvents(DepartureRestrictionUpdatedEvent::class);
        $restriction = $this->service->createAverageDepartureInterval(
            4,
            'EGLL',
            ['TEST1X', 'TEST1Y'],
            Carbon::now()->addMinutes(10)
        );

        $this->service->updateDepartureRestriction(
            $restriction->id,
            2,
            'EGLL',
            ['TEST1X'],
            Carbon::now()->addMinutes(20)
        );

        $this->assertDatabaseHas(
            'departure_restrictions',
            [
                'id' => $restriction->id,
                'type_id' => 2,
                'interval' => 2,
                'expires_at' => Carbon::now()->addMinutes(20),
            ]
        );

        $this->assertDatabaseHas(
            'departure_restriction_sid',
            [
                'departure_restriction_id' => $restriction->id,
                'sid_id' => 1,
            ]
        );

        $this->assertDatabaseMissing(
            'departure_restriction_sid',
            [
                'departure_restriction_id' => $restriction->id,
                'sid_id' => 2,
            ]
        );
    }

    public function testItExpiresRestrictions()
    {
        $this->expectsEvents(DepartureRestrictionUpdatedEvent::class);
        $restriction = $this->service->createAverageDepartureInterval(
            4,
            'EGLL',
            ['TEST1X', 'TEST1Y'],
            Carbon::now()->addMinutes(10)
        );

        $this->service->expireDepartureRestriction($restriction->id);
        $restriction->refresh();
        $this->assertTrue($restriction->expired());
    }

    public function testItReturnsActiveRestrictions()
    {
        $this->withoutEvents();

        $restriction1 = $this->service->createAverageDepartureInterval(
            4,
            'EGLL',
            ['TEST1X', 'TEST1Y'],
            Carbon::now()->addSecond()
        );

        $this->service->createAverageDepartureInterval(
            4,
            'EGLL',
            ['TEST1X', 'TEST1Y'],
            Carbon::now()
        );

        $restriction3 = $this->service->createMinimumDepartureInterval(
            2,
            'EGLL',
            ['TEST1X', 'TEST1Y'],
            Carbon::now()->addMinute()
        );

        $this->service->createMinimumDepartureInterval(
            2,
            'EGLL',
            ['TEST1X', 'TEST1Y'],
            Carbon::now()->subSecond()
        );

        $expected = new Collection([
            DepartureRestriction::with('sids', 'sids.airfield')->find($restriction1->id),
            DepartureRestriction::with('sids', 'sids.airfield')->find($restriction3->id)
        ]);
        $this->assertEquals($expected, $this->service->getActiveRestrictions());
    }

    public function testItReturnsUkWakeIntervalData()
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

        $this->assertEquals($expected, $this->service->getDepartureUkWakeIntervalsDependency());
    }

    public function testItReturnsRecatWakeIntervalData()
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

        $this->assertEquals($expected, $this->service->getDepartureRecatWakeIntervalsDependency());
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

        $this->assertEquals($expected, $this->service->getDepartureIntervalGroupsDependency());
    }
}
