<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddGlasgowHolds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $fyner = DB::table('navaids')->insertGetId(
            [
                'identifier' => 'FYNER',
                'latitude' => 'N056.02.56.000',
                'longitude' => 'W005.06.55.000',
                'created_at' => Carbon::now(),
            ]
        );

        $foyle = DB::table('navaids')->insertGetId(
            [
                'identifier' => 'FOYLE',
                'latitude' => 'N056.08.34.000',
                'longitude' => 'W004.22.56.000',
                'created_at' => Carbon::now(),
            ]
        );

        $fynerPublished = DB::table('holds')->insertGetId(
            [
                'navaid_id' => $fyner,
                'description' => 'FYNER',
                'inbound_heading' => '118',
                'minimum_altitude' => 7000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'left',
                'created_at' => Carbon::now(),
            ]
        );

        $foylePublished = DB::table('holds')->insertGetId(
            [
                'navaid_id' => $foyle,
                'description' => 'FOYLE',
                'inbound_heading' => '190',
                'minimum_altitude' => 7000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'left',
                'created_at' => Carbon::now(),
            ]
        );

        DB::table('hold_restriction')->insert(
            [
                [
                    'hold_id' => $foylePublished,
                    'restriction' => json_encode(
                        [
                            'type' => 'minimum_level',
                            'level' => 'MSL',
                            'target' => 'EGPF',
                        ]
                    ),
                    'created_at' => Carbon::now(),
                ],
                [
                    'hold_id' => $fynerPublished,
                    'restriction' => json_encode(
                        [
                            'type' => 'minimum_level',
                            'level' => 'MSL',
                            'target' => 'EGPF',
                        ]
                    ),
                    'created_at' => Carbon::now(),
                ],
            ]
        );

        DependencyService::touchDependencyByKey('DEPENDENCY_NAVAIDS');
        DependencyService::touchDependencyByKey('DEPENDENCY_HOLDS');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('navaids')->whereIn('identifier', ['FOYLE', 'FYNER'])->delete();
        DependencyService::touchDependencyByKey('DEPENDENCY_NAVAIDS');
        DependencyService::touchDependencyByKey('DEPENDENCY_HOLDS');
    }
}
