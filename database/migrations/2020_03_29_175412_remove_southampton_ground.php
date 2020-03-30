<?php

use App\Models\Airfield\Airfield;
use App\Services\AirfieldService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveSouthamptonGround extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        AirfieldService::removeFromTopDownsOrder('EGHI', 'EGHI_GND');
        DB::table('controller_positions')
            ->where('callsign', 'EGHI_GND')
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('controller_positions')
            ->insert(
                [
                    'callsign' => 'EGHI_GND',
                    'frequency' => 121.770,
                    'created_at' => Carbon::now(),
                ]
            );

        AirfieldService::insertIntoOrderBefore('EGHI', 'EGHI_GND', 'EGHI_TWR');
    }
}
