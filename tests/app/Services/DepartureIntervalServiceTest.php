<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\DepartureIntervalUpdatedEvent;
use App\Models\Departure\DepartureInterval;
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
}
