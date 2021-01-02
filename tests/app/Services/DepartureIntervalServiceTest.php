<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;

class DepartureIntervalServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var DepartureIntervalService
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(DepartureIntervalService::class);
        Carbon::setTestNow(Carbon::now()->startOfSecond());
    }

    public function testItCreatesAMinimumDepartureIntervalWithSids()
    {
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
}
