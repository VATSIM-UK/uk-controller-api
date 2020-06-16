<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddExtraNavaids extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('navaids')->insert(
            [
                [
                    'identifier' => 'TNT',
                    'latitude' => 'N053.03.14.000',
                    'longitude' => 'W001.40.12.000',
                    'created_at' => Carbon::now(),
                ],
                [
                    'identifier' => 'SITKU',
                    'latitude' => 'N053.44.36.000',
                    'longitude' => 'W004.49.54.000',
                    'created_at' => Carbon::now(),
                ],
                [
                    'identifier' => 'PENIL',
                    'latitude' => 'N053.36.57.000',
                    'longitude' => 'W003.39.49.000',
                    'created_at' => Carbon::now(),
                ],
                [
                    'identifier' => 'BHD',
                    'latitude' => 'N050.23.55.000',
                    'longitude' => 'W003.29.37.000',
                    'created_at' => Carbon::now(),
                ],
            ]
        );
        DependencyService::touchDependencyByKey('DEPENDENCY_NAVAIDS');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('navaids')->whereIn('identifier', ['TNT', 'SITKU', 'PENIL', 'BHD'])->delete();
        DependencyService::touchDependencyByKey('DEPENDENCY_NAVAIDS');
    }
}
