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
                    'local_file' => 'airfield-ownership.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_CONTROLLER_POSITIONS',
                    'local_file' => 'controller-positions.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_HOLDS',
                    'local_file' => 'holds.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_INITIAL_ALTITUDES',
                    'local_file' => 'initial-altitudes.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_HANDOFF',
                    'local_file' => 'handoffs.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_HOLD_PROFILE',
                    'local_file' => 'holds-profiles.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_WAKE',
                    'local_file' => 'wake-categories.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_PRENOTE',
                    'local_file' => 'prenotes.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_ASR',
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
