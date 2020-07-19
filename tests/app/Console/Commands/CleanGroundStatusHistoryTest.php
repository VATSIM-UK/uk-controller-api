<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CleanGroundStatusHistoryTest extends BaseFunctionalTestCase
{
    const TABLE = 'ground_status_history';

    public function testItDeletesOldAssignments()
    {
        Carbon::setTestNow(Carbon::now());
        DB::table(self::TABLE)->insert(
            [
                [
                    'callsign' => 'BAW123',
                    'ground_status_id' => 4,
                    'assigned_at' => Carbon::now()->subMonths(3)->addSecond(),
                ],
                [
                    'callsign' => 'RYR456',
                    'ground_status_id' => 1,
                    'assigned_at' => Carbon::now()->subMonths(3),
                ],
                [
                    'callsign' => 'EZY890',
                    'ground_status_id' => 5,
                    'assigned_at' => Carbon::now()->subMonths(3)->subSecond(),
                ],

                [
                    'callsign' => 'VIR25F',
                    'ground_status_id' => 2,
                    'assigned_at' => Carbon::now()->subMonths(3)->subHour(),
                ],
            ]
        );

        Artisan::call('ground-status:clean-history');
        $this->assertDatabaseCount(self::TABLE, 2);
        $this->assertDatabaseHas(
            self::TABLE,
            [
                'callsign' => 'BAW123',
            ]
        );
        $this->assertDatabaseHas(
            self::TABLE,
            [
                'callsign' => 'RYR456',
            ]
        );
    }
}
