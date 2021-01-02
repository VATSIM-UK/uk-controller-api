<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\DepartureIntervalUpdatedEvent;
use App\Models\Aircraft\WakeCategory;
use App\Models\Departure\DepartureInterval;
use App\Models\Sid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DepartureIntervalServiceTest extends BaseFunctionalTestCase
{
    private DepartureIntervalService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(DepartureIntervalService::class);
        Carbon::setTestNow(Carbon::now()->startOfSecond());
    }

    public function testItCreatesAMinimumDepartureIntervalWithSids()
    {
        $this->expectsEvents(DepartureIntervalUpdatedEvent::class);
        $interval = $this->service->createMinimumDepartureInterval(
            4,
            'EGLL',
            ['TEST1X', 'TEST1Y'],
            Carbon::now()->addMinutes(10)
        );

        $this->assertDatabaseHas(
            'departure_intervals',
            [
                'id' => $interval->id,
                'type_id' => 1,
                'interval' => 4,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]
        );

        $this->assertDatabaseHas(
            'departure_interval_sid',
            [
                'departure_interval_id' => $interval->id,
                'sid_id' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'departure_interval_sid',
            [
                'departure_interval_id' => $interval->id,
                'sid_id' => 2,
            ]
        );
    }

    public function testItCreatesAnAverageDepartureIntervalWithSids()
    {
        $this->expectsEvents(DepartureIntervalUpdatedEvent::class);
        $interval = $this->service->createAverageDepartureInterval(
            4,
            'EGLL',
            ['TEST1X', 'TEST1Y'],
            Carbon::now()->addMinutes(10)
        );

        $this->assertDatabaseHas(
            'departure_intervals',
            [
                'id' => $interval->id,
                'type_id' => 2,
                'interval' => 4,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]
        );

        $this->assertDatabaseHas(
            'departure_interval_sid',
            [
                'departure_interval_id' => $interval->id,
                'sid_id' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'departure_interval_sid',
            [
                'departure_interval_id' => $interval->id,
                'sid_id' => 2,
            ]
        );
    }

    public function testItUpdatesIntervals()
    {
        $this->expectsEvents(DepartureIntervalUpdatedEvent::class);
        $interval = $this->service->createAverageDepartureInterval(
            4,
            'EGLL',
            ['TEST1X', 'TEST1Y'],
            Carbon::now()->addMinutes(10)
        );

        $this->service->updateDepartureInterval(
            $interval->id,
            2,
            'EGLL',
            ['TEST1X'],
            Carbon::now()->addMinutes(20)
        );

        $this->assertDatabaseHas(
            'departure_intervals',
            [
                'id' => $interval->id,
                'type_id' => 2,
                'interval' => 2,
                'expires_at' => Carbon::now()->addMinutes(20),
            ]
        );

        $this->assertDatabaseHas(
            'departure_interval_sid',
            [
                'departure_interval_id' => $interval->id,
                'sid_id' => 1,
            ]
        );

        $this->assertDatabaseMissing(
            'departure_interval_sid',
            [
                'departure_interval_id' => $interval->id,
                'sid_id' => 2,
            ]
        );
    }

    public function testItExpiresIntervals()
    {
        $this->expectsEvents(DepartureIntervalUpdatedEvent::class);
        $interval = $this->service->createAverageDepartureInterval(
            4,
            'EGLL',
            ['TEST1X', 'TEST1Y'],
            Carbon::now()->addMinutes(10)
        );

        $this->service->expireDepartureInterval($interval->id);
        $interval->refresh();
        $this->assertTrue($interval->expired());
    }

    public function testItReturnsActiveIntervals()
    {
        $this->withoutEvents();

        $interval1 = $this->service->createAverageDepartureInterval(
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

        $interval3 = $this->service->createMinimumDepartureInterval(
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
            DepartureInterval::with('sids', 'sids.airfield')->find($interval1->id),
            DepartureInterval::with('sids', 'sids.airfield')->find($interval3->id)
        ]);
        $this->assertEquals($expected, $this->service->getActiveIntervals());
    }

    public function testItReturnsWakeIntervalData()
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

        $this->assertEquals($expected, $this->service->getDepartureWakeIntervalsDependency());
    }

    public function testItReturnsSidIntervalData()
    {
        DB::table('departure_sid_intervals')->delete();
        Sid::find(1)->departureIntervals()->sync(
            [1 => ['interval' => 25], 2 => ['interval' => 23]],
        );

        Sid::find(2)->departureIntervals()->sync(
            [1 => ['interval' => 26], 2 => ['interval' => 52]],
        );

        Sid::find(3)->departureIntervals()->sync(
            [3 => ['interval' => 99]],
        );

        $expected = [
            'EGLL' => [
                [
                    'lead' => 'TEST1X',
                    'follow' => 'TEST1X',
                    'interval' => 25,
                ],
                [
                    'lead' => 'TEST1X',
                    'follow' => 'TEST1Y',
                    'interval' => 23,
                ],
                [
                    'lead' => 'TEST1Y',
                    'follow' => 'TEST1X',
                    'interval' => 26,
                ],
                [
                    'lead' => 'TEST1Y',
                    'follow' => 'TEST1Y',
                    'interval' => 52,
                ],
            ],
            'EGBB' => [
                [
                    'lead' => 'TEST1A',
                    'follow' => 'TEST1A',
                    'interval' => 99,
                ],
            ],
        ];

        $this->assertEquals($expected, $this->service->getDepartureSidIntervalsDependency());
    }
}
