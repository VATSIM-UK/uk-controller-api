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
                    'key' => 'DEPENDENCY_AIRFIELD_OWNERSHIP',
                    'uri' => 'airfield-ownership',
                    'local_file' => 'airfield-ownership.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_CONTROLLER_POSITIONS',
                    'uri' => 'controller-positions',
                    'local_file' => 'controller-positions.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_HOLDS',
                    'uri' => 'hold',
                    'local_file' => 'holds.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_INITIAL_ALTITUDES',
                    'uri' => 'initial-altitude',
                    'local_file' => 'initial-altitudes.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_HOLD_PROFILE',
                    'uri' => 'hold/profile',
                    'local_file' => 'holds-profiles.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_WAKE',
                    'uri' => 'wake-category/dependency',
                    'local_file' => 'wake-categories.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_PRENOTE',
                    'uri' => 'prenote',
                    'local_file' => 'prenotes.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_ASR',
                    'uri' => 'altimeter-setting-region',
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
