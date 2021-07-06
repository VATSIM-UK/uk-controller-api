<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CleanPrenoteMessageHistoryTest extends BaseFunctionalTestCase
{
    public function testItDeletesOldAssignments()
    {
        Carbon::setTestNow(Carbon::now());
        DB::table('prenote_messages')->insert(
            [
                [
                    'callsign' => 'BAW123',
                    'user_id' => self::ACTIVE_USER_CID,
                    'departure_airfield' => 'EGLL',
                    'controller_position_id' => 1,
                    'target_controller_position_id' => 2,
                    'created_at' => Carbon::now()->subMonths(3)->addSecond(),
                    'expires_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'RYR456',
                    'user_id' => self::ACTIVE_USER_CID,
                    'departure_airfield' => 'EGLL',
                    'controller_position_id' => 1,
                    'target_controller_position_id' => 2,
                    'created_at' => Carbon::now()->subMonths(3),
                    'expires_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EZY890',
                    'user_id' => self::ACTIVE_USER_CID,
                    'departure_airfield' => 'EGLL',
                    'controller_position_id' => 1,
                    'target_controller_position_id' => 2,
                    'created_at' => Carbon::now()->subMonths(3)->subSecond(),
                    'expires_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'VIR25F',
                    'user_id' => self::ACTIVE_USER_CID,
                    'departure_airfield' => 'EGLL',
                    'controller_position_id' => 1,
                    'target_controller_position_id' => 2,
                    'created_at' => Carbon::now()->subMonths(3)->subHour(),
                    'expires_at' => Carbon::now(),
                ],
            ]
        );

        Artisan::call('prenote-messages:clean-history');
        $this->assertDatabaseCount('prenote_messages', 2);
        $this->assertDatabaseHas(
            'prenote_messages',
            [
                'callsign' => 'BAW123',
            ]
        );
        $this->assertDatabaseHas(
            'prenote_messages',
            [
                'callsign' => 'RYR456',
            ]
        );
    }
}
