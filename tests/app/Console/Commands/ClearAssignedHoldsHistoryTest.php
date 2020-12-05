<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ClearAssignedHoldsHistoryTest extends BaseFunctionalTestCase
{
    public function testItDeletesOldAssignments()
    {
        Carbon::setTestNow(Carbon::now());
        DB::table('assigned_holds_history')->insert(
            [
                [
                    'callsign' => 'BAW123',
                    'navaid_id' => 1,
                    'assigned_at' => Carbon::now()->subMonths(3)->addSecond(),
                ],
                [
                    'callsign' => 'RYR456',
                    'navaid_id' => 2,
                    'assigned_at' => Carbon::now()->subMonths(3),
                ],
                [
                    'callsign' => 'EZY890',
                    'stand_id' => 1,
                    'assigned_at' => Carbon::now()->subMonths(3)->subSecond(),
                ],

                [
                    'callsign' => 'VIR25F',
                    'navaid_id' => 2,
                    'assigned_at' => Carbon::now()->subMonths(3)->subHour(),
                ],
            ]
        );

        Artisan::call('holds:clean-history');
        $this->assertDatabaseCount('assigned_holds_history', 2);
        $this->assertDatabaseHas(
            'assigned_holds_history',
            [
                'callsign' => 'BAW123',
            ]
        );
        $this->assertDatabaseHas(
            'assigned_holds_history',
            [
                'callsign' => 'RYR456',
            ]
        );
    }
}
