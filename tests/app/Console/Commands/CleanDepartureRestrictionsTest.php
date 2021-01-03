<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Models\Departure\DepartureRestriction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class CleanDepartureRestrictionsTest extends BaseFunctionalTestCase
{
    public function testItDeletesOldRestrictions()
    {
        Carbon::setTestNow(Carbon::now());
        DepartureRestriction::insert(
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

        Artisan::call('departure-restrictions:clean');
        $this->assertDatabaseCount('departure_restrictions', 2);
        $this->assertDatabaseHas(
            'departure_restrictions',
            [
                'id' => 1,
            ]
        );
        $this->assertDatabaseHas(
            'departure_restrictions',
            [
                'id' => 2,
            ]
        );
    }
}
