<?php

use App\Models\Hold\Hold;
use App\Models\Hold\HoldRestriction;
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
        DB::table('hold')->insert(
            [
                [
                    'fix' => 'GODLU',
                    'inbound_heading' => 310,
                    'minimum_altitude' => 8000,
                    'maximum_altitude' => 12000,
                    'turn_direction' => 'right',
                    'description' => 'GODLU',
                    'created_at' => Carbon::now(),
                ],
                [
                    'fix' => 'JACKO',
                    'inbound_heading' => 84,
                    'minimum_altitude' => 8000,
                    'maximum_altitude' => 14000,
                    'turn_direction' => 'left',
                    'description' => 'JACKO',
                    'created_at' => Carbon::now(),
                ],
                [
                    'fix' => 'LCY',
                    'inbound_heading' => 273,
                    'minimum_altitude' => 2000,
                    'maximum_altitude' => 2000,
                    'turn_direction' => 'right',
                    'description' => 'LCY',
                    'created_at' => Carbon::now(),
                ],
            ]
        );

        // Add some restrictions
        $restrictionData = [
            'restriction' => [
                'type' => 'minimum-level',
                'level' => 'MSL',
                'target' => 'EGLC',
            ],
        ];

        Hold::where('fix', 'GODLU')->firstOrFail()->restrictions()->save(HoldRestriction::make($restrictionData));
        Hold::where('fix', 'JACKO')->firstOrFail()->restrictions()->save(HoldRestriction::make($restrictionData));
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
