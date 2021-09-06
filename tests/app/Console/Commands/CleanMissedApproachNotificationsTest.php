<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CleanMissedApproachNotificationsTest extends BaseFunctionalTestCase
{
    public function testItDeletesOldAssignments()
    {
        Carbon::setTestNow(Carbon::now());
        DB::table('missed_approach_notifications')->insert(
            [
                [
                    'callsign' => 'BAW123',
                    'user_id' => self::ACTIVE_USER_CID,
                    'created_at' => Carbon::now()->subMonths(3)->addSecond(),
                    'expires_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'RYR456',
                    'user_id' => self::ACTIVE_USER_CID,
                    'created_at' => Carbon::now()->subMonths(3),
                    'expires_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EZY890',
                    'user_id' => self::ACTIVE_USER_CID,
                    'created_at' => Carbon::now()->subMonths(3)->subSecond(),
                    'expires_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'VIR25F',
                    'user_id' => self::ACTIVE_USER_CID,
                    'created_at' => Carbon::now()->subMonths(3)->subHour(),
                    'expires_at' => Carbon::now(),
                ],
            ]
        );

        Artisan::call('missed-approaches:clean-history');
        $this->assertDatabaseCount('missed_approach_notifications', 2);
        $this->assertDatabaseHas(
            'missed_approach_notifications',
            [
                'callsign' => 'BAW123',
            ]
        );
        $this->assertDatabaseHas(
            'missed_approach_notifications',
            [
                'callsign' => 'RYR456',
            ]
        );
    }
}
