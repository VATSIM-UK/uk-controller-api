<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Models\Stand\StandAssignmentsHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CleanPluginEventsTest extends BaseFunctionalTestCase
{
    public function testItDeletesOldEvents()
    {
        Carbon::setTestNow(Carbon::now());
        DB::table('plugin_events')->insert(
            [
                [
                    'id' => 1,
                    'event' => json_encode(['foo' => 'bar']),
                    'created_at' => Carbon::now()->subHour()->addSecond(),
                ],
                [
                    'id' => 2,
                    'event' => json_encode(['foo' => 'bar']),
                    'assigned_at' => Carbon::now()->subHour(),
                ],
                [
                    'id' => 3,
                    'event' => json_encode(['foo' => 'bar']),
                    'assigned_at' => Carbon::now()->subHour()->subSecond(),
                ],

                [
                    'id' => 4,
                    'event' => json_encode(['foo' => 'bar']),
                    'assigned_at' => Carbon::now()->subHour()->subDay(),
                ],
            ]
        );

        Artisan::call('plugin-events:clean');
        $this->assertDatabaseCount('plugin_events', 2);
        $this->assertDatabaseHas(
            'plugin_events',
            [
                'id' => 1,
            ]
        );
        $this->assertDatabaseHas(
            'plugin_events',
            [
                'id' => 2,
            ]
        );
    }
}
