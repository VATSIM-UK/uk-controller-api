<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddControllerPositionAbbreviations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('controller_position_alternative_callsigns')
            ->insert(
                [
                    [
                        'controller_position_id' => DB::table('controller_positions')->where(
                            'callsign',
                            'ESSEX_APP'
                        )->first()->id,
                        'callsign' => 'ESX_APP',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'controller_position_id' => DB::table('controller_positions')->where(
                            'callsign',
                            'SOLENT_APP'
                        )->first()->id,
                        'callsign' => 'SOL_APP',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'controller_position_id' => DB::table('controller_positions')->where(
                            'callsign',
                            'THAMES_APP'
                        )->first()->id,
                        'callsign' => 'THA_APP',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'controller_position_id' => DB::table('controller_positions')->where(
                            'callsign',
                            'THAMES_APP'
                        )->first()->id,
                        'callsign' => 'TMS_APP',
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
        //
    }
}
