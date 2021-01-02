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

        $interval2 = DepartureInterval::create(
            [
                'interval' => 4,
                'type_id' => DepartureIntervalType::where('key', 'mdi')->first()->id,
                'expires_at' => Carbon::now()->addSecond()
            ]
        );

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'departure/intervals')
            ->assertOk()
            ->assertJson([$interval1->toArray(), $interval2->toArray()]);
    }
}
