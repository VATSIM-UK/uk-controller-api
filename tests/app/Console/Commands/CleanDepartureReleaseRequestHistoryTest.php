<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CleanDepartureReleaseRequestHistoryTest extends BaseFunctionalTestCase
{
    public function testItDeletesOldAssignments()
    {
        Carbon::setTestNow(Carbon::now());
        DB::table('departure_release_requests')->insert(
            [
                [
                    'callsign' => 'BAW123',
                    'user_id' => self::ACTIVE_USER_CID,
                    'controller_position_id' => 1,
                    'target_controller_position_id' => 2,
                    'created_at' => Carbon::now()->subMonths(3)->addSecond(),
                    'expires_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'RYR456',
                    'user_id' => self::ACTIVE_USER_CID,
                    'controller_position_id' => 1,
                    'target_controller_position_id' => 2,
                    'created_at' => Carbon::now()->subMonths(3),
                    'expires_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EZY890',
                    'user_id' => self::ACTIVE_USER_CID,
                    'controller_position_id' => 1,
                    'target_controller_position_id' => 2,
                    'created_at' => Carbon::now()->subMonths(3)->subSecond(),
                    'expires_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'VIR25F',
                    'user_id' => self::ACTIVE_USER_CID,
                    'controller_position_id' => 1,
                    'target_controller_position_id' => 2,
                    'created_at' => Carbon::now()->subMonths(3)->subHour(),
                    'expires_at' => Carbon::now(),
                ],
            ]
        );

        Artisan::call('departure-releases:clean-history');
        $this->assertDatabaseCount('departure_release_requests', 2);
        $this->assertDatabaseHas(
            'departure_release_requests',
            [
                'callsign' => 'BAW123',
            ]
        );
        $this->assertDatabaseHas(
            'departure_release_requests',
            [
                'callsign' => 'RYR456',
            ]
        );
    }
}
