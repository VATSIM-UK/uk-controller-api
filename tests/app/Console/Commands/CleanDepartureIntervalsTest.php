<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Models\Departure\DepartureInterval;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class CleanDepartureIntervalsTest extends BaseFunctionalTestCase
{
    public function testItDeletesOldAssignments()
    {
        Carbon::setTestNow(Carbon::now());
        DepartureInterval::insert(
            [
                [
                    'type_id' => 1,
                    'interval' => 2,
                    'expires_at' => Carbon::now()->subMonths(3)->addSecond(),
                ],
                [
                    'type_id' => 1,
                    'interval' => 2,
                    'expires_at' => Carbon::now()->subMonths(3),
                ],
                [
                    'type_id' => 1,
                    'interval' => 2,
                    'expires_at' => Carbon::now()->subMonths(3)->subSecond(),
                ],

                [
                    'type_id' => 1,
                    'interval' => 2,
                    'expires_at' => Carbon::now()->subMonths(3)->subHour(),
                ],
            ]
        );

        Artisan::call('departure-intervals:clean');
        $this->assertDatabaseCount('departure_intervals', 2);
        $this->assertDatabaseHas(
            'departure_intervals',
            [
                'id' => 1,
            ]
        );
        $this->assertDatabaseHas(
            'departure_intervals',
            [
                'id' => 2,
            ]
        );
    }
}
