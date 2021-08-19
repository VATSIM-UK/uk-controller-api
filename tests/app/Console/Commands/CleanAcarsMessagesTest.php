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
                    'message' => 'foo1',
                    'successful' => true,
                    'created_at' => Carbon::now()->subMonth()->addSecond(),
                ],
                [
                    'message' => 'foo2',
                    'successful' => true,
                    'created_at' => Carbon::now()->subMonth(),
                ],
                [
                    'message' => 'foo3',
                    'successful' => true,
                    'created_at' => Carbon::now()->subMonth()->subSecond(),
                ],
                [
                    'message' => 'foo4',
                    'successful' => true,
                    'created_at' => Carbon::now()->subMonth()->subHour(),
                ],
            ]
        );

        Artisan::call('acars:clean-history');
        $this->assertDatabaseCount('acars_messages', 2);
        $this->assertDatabaseHas(
            'acars_messages',
            [
                'message' => 'foo1',
            ]
        );
        $this->assertDatabaseHas(
            'acars_messages',
            [
                'message' => 'foo2',
            ]
        );
    }
}
