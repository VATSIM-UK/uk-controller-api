<?php

use App\Models\Airfield;
use App\Models\Hold\Hold;
use App\Models\Hold\HoldRestriction;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class NewFarnboroughHolds extends Migration
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
                    'fix' => 'PEPIS',
                    'inbound_heading' => 4,
                    'minimum_altitude' => 7000,
                    'maximum_altitude' => 10000,
                    'turn_direction' => 'right',
                    'description' => 'EGLF PEPIS',
                    'created_at' => Carbon::now(),
                ],
                [
                    'fix' => 'RUDMO',
                    'inbound_heading' => 276,
                    'minimum_altitude' => 8000,
                    'maximum_altitude' => 11000,
                    'turn_direction' => 'left',
                    'description' => 'EGLF RUDMO',
                    'created_at' => Carbon::now(),
                ],
                [
                    'fix' => 'VEXUB',
                    'inbound_heading' => 57,
                    'minimum_altitude' => 3000,
                    'maximum_altitude' => 3000,
                    'turn_direction' => 'left',
                    'description' => 'EGLF VEXUB',
                    'created_at' => Carbon::now(),
                ],
            ]
        );

        // Add some restrictions
        $restrictionData = [
            'restriction' => [
                'type' => 'minimum-level',
                'level' => 'MSL',
                'target' => 'EGLF',
            ],
        ];

        Hold::where('fix', 'PEPIS')->firstOrFail()->restrictions()->save(HoldRestriction::make($restrictionData));
        Hold::where('fix', 'RUDMO')->firstOrFail()->restrictions()->save(HoldRestriction::make($restrictionData));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Delete new Holds
        DB::table('hold')->whereIn('fix', ['PEPIS', 'RUDMO', 'VEXUB'])->delete();
    }
}
