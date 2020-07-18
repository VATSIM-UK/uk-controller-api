<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Models\Squawk\SquawkAssignmentsHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class CleanSquawkAssignmentsHistoryTest extends BaseFunctionalTestCase
{
    public function testItDeletesOldAssignments()
    {
        Carbon::setTestNow(Carbon::now());
        SquawkAssignmentsHistory::insert(
            [
                [
                    'callsign' => 'BAW123',
                    'type' => 'CCAMS',
                    'code' => '0001',
                    'allocated_at' => Carbon::now()->subMonths(3)->addSecond(),
                ],
                [
                    'callsign' => 'RYR456',
                    'type' => 'CCAMS',
                    'code' => '0002',
                    'allocated_at' => Carbon::now()->subMonths(3),
                ],
                [
                    'callsign' => 'EZY890',
                    'type' => 'CCAMS',
                    'code' => '0003',
                    'allocated_at' => Carbon::now()->subMonths(3)->subSecond(),
                ],

                [
                    'callsign' => 'VIR25F',
                    'type' => 'CCAMS',
                    'code' => '0004',
                    'allocated_at' => Carbon::now()->subMonths(3)->subHour(),
                ],
            ]
        );

        Artisan::call('squawks:clean-history');
        $this->assertDatabaseCount('squawk_assignments_history', 2);
        $this->assertDatabaseHas(
            'squawk_assignments_history',
            [
                'callsign' => 'BAW123',
            ]
        );
        $this->assertDatabaseHas(
            'squawk_assignments_history',
            [
                'callsign' => 'RYR456',
            ]
        );
    }
}
