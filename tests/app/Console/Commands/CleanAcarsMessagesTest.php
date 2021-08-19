<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CleanAcarsMessagesTest extends BaseFunctionalTestCase
{
    public function testItDeletesOldAssignments()
    {
        Carbon::setTestNow(Carbon::now());
        DB::table('acars_messages')->insert(
            [
                [
                    'callsign' => 'BAW123',
                    'message' => 'foo',
                    'created_at' => Carbon::now()->subMonth()->addSecond(),
                ],
                [
                    'callsign' => 'RYR456',
                    'message' => 'foo',
                    'created_at' => Carbon::now()->subMonth(),
                ],
                [
                    'callsign' => 'EZY890',
                    'message' => 'foo',
                    'created_at' => Carbon::now()->subMonth()->subSecond(),
                ],
                [
                    'callsign' => 'VIR25F',
                    'message' => 'foo',
                    'created_at' => Carbon::now()->subMonth()->subHour(),
                ],
            ]
        );

        Artisan::call('acars:clean-history');
        $this->assertDatabaseCount('acars_messages', 2);
        $this->assertDatabaseHas(
            'acars_messages',
            [
                'callsign' => 'BAW123',
            ]
        );
        $this->assertDatabaseHas(
            'acars_messages',
            [
                'callsign' => 'RYR456',
            ]
        );
    }
}
