<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Models\Stand\StandAssignmentsHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class CleanStandAssignmentsHistoryTest extends BaseFunctionalTestCase
{
    public function testItDeletesOldAssignments()
    {
        Carbon::setTestNow(Carbon::now());
        StandAssignmentsHistory::insert(
            [
                [
                    'callsign' => 'BAW123',
                    'stand_id' => 1,
                    'assigned_at' => Carbon::now()->subMonths(3)->addSecond(),
                ],
                [
                    'callsign' => 'RYR456',
                    'stand_id' => 2,
                    'assigned_at' => Carbon::now()->subMonths(3),
                ],
                [
                    'callsign' => 'EZY890',
                    'stand_id' => 1,
                    'assigned_at' => Carbon::now()->subMonths(3)->subSecond(),
                ],

                [
                    'callsign' => 'VIR25F',
                    'stand_id' => 3,
                    'assigned_at' => Carbon::now()->subMonths(3)->subHour(),
                ],
            ]
        );

        Artisan::call('stands:clean-history');
        $this->assertDatabaseCount('stand_assignments_history', 2);
        $this->assertDatabaseHas(
            'stand_assignments_history',
            [
                'callsign' => 'BAW123',
            ]
        );
        $this->assertDatabaseHas(
            'stand_assignments_history',
            [
                'callsign' => 'RYR456',
            ]
        );
    }
}
