<?php

use App\Models\Dependency\Dependency;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddDependencies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Dependency::insert(
            [
                [
                    'key' => 'DEPENDENCY_AIRCRAFT',
                    'uri' => '/aircraft',
                    'local_file' => 'aircraft.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_SID',
                    'uri' => '/sid',
                    'local_file' => 'sids.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_AIRFIELD',
                    'uri' => '/airfield',
                    'local_file' => 'airfields.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_CONTROLLER',
                    'uri' => '/controller',
                    'local_file' => 'controllers.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_HANDOFF',
                    'uri' => '/handoff',
                    'local_file' => 'handoffs.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_HOLD',
                    'uri' => '/hold',
                    'local_file' => 'holds.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_HOLD_PROFILE',
                    'uri' => '/hold/profile',
                    'local_file' => 'holds-profiles.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_WAKE',
                    'uri' => '/wake-category',
                    'local_file' => 'wake-categories.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_PRENOTE',
                    'uri' => '/prenote',
                    'local_file' => 'prenotes.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_ASR',
                    'uri' => '/altimeter-setting-region',
                    'local_file' => 'altimeter-setting-regions.json',
                    'created_at' => Carbon::now(),
                ],
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Dependency::truncate();
    }
}
