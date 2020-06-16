<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class EglcRnavHolds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add new holds
        $godlu = DB::table('hold')->insertGetId(
            [
                'fix' => 'GODLU',
                'inbound_heading' => 310,
                'minimum_altitude' => 8000,
                'maximum_altitude' => 12000,
                'turn_direction' => 'right',
                'description' => 'GODLU',
                'created_at' => Carbon::now(),
            ]
        );

        $jacko = DB::table('hold')->insertGetId(
            [
                'fix' => 'JACKO',
                'inbound_heading' => 84,
                'minimum_altitude' => 8000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'left',
                'description' => 'JACKO',
                'created_at' => Carbon::now(),
            ]
        );

        DB::table('hold')->insert(
            [
                'fix' => 'LCY',
                'inbound_heading' => 273,
                'minimum_altitude' => 2000,
                'maximum_altitude' => 2000,
                'turn_direction' => 'right',
                'description' => 'LCY',
                'created_at' => Carbon::now(),
            ]
        );

        // Add some restrictions
        $restrictionData = [
            'restriction' => json_encode([
                'type' => 'minimum-level',
                'level' => 'MSL',
                'target' => 'EGLC',
                'created_at' => Carbon::now(),
            ]),
        ];

        DB::table('hold_restriction')->insert(
            [
                array_merge($restrictionData, ['hold_id' => $godlu]),
                array_merge($restrictionData, ['hold_id' => $jacko]),
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
        // Delete new Holds
        DB::table('hold')->whereIn('fix', ['JACKO', 'GODLU', 'LCY'])->delete();
    }
}
