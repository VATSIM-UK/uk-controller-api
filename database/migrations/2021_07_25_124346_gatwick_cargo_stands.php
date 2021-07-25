<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GatwickCargoStands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $gatwick = DB::table('airfield')->where('code', 'EGKK')->first()->id;
        $cargoType = DB::table('stand_types')->where('key', 'CARGO')->first()->id;

        // Update the 15x stands to be higher-priority cargo
        DB::table('stands')
            ->whereIn('identifier', ['153', '154', '158', '159'])
            ->update(
                [
                    'assignment_priority' => 99,
                    'type_id' => $cargoType,
                    'updated_at' => Carbon::now(),
                ]
            );

        // Update the 23x stands to be lower-priority cargo
        DB::table('stands')
            ->whereIn(
                'identifier',
                [
                    '230L',
                    '230',
                    '230R',
                    '231L',
                    '231',
                    '231R',
                    '232L',
                    '232',
                    '232R',
                    '233L',
                    '233',
                    '233R',
                    '234L',
                    '234',
                    '234R',
                    '235L',
                    '235',
                    '235R',
                ]
            )
            ->update(
                [
                    'type_id' => $cargoType,
                    'updated_at' => Carbon::now(),
                ]
            );

        // Update the 151/2 stands to be lowest-priority cargo stands as wrong side of the taxiway
        DB::table('stands')
            ->whereIn(
                'identifier',
                [
                    '150L',
                    '150',
                    '150R',
                    '151L',
                    '151',
                    '151R',
                    '152L',
                    '152',
                    '152R',
                ]
            )
            ->update(
                [
                    'assignment_priority' => 101,
                    'type_id' => $cargoType,
                    'updated_at' => Carbon::now(),
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
