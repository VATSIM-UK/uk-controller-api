<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCcamsRangeData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('ccams_squawk_ranges')->insert(
            [
                [
                    'start' => '0201',
                    'stop' => '0277',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '0301',
                    'stop' => '0377',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '0470',
                    'stop' => '0477',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '0501',
                    'stop' => '0577',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '0730',
                    'stop' => '0767',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '1070',
                    'stop' => '1077',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '1140',
                    'stop' => '1176',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '1410',
                    'stop' => '1477',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '2001',
                    'stop' => '2077',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '2150',
                    'stop' => '2177',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '2201',
                    'stop' => '2277',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '2701',
                    'stop' => '2737',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '3201',
                    'stop' => '3277',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '3370',
                    'stop' => '3377',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '3401',
                    'stop' => '3477',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '3510',
                    'stop' => '3537',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '4215',
                    'stop' => '4247',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '4430',
                    'stop' => '4477',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '4610',
                    'stop' => '4667',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '4701',
                    'stop' => '4777',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '5013',
                    'stop' => '5017',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '5201',
                    'stop' => '5270',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '5401',
                    'stop' => '5477',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '5660',
                    'stop' => '5664',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '5565',
                    'stop' => '5676',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '6201',
                    'stop' => '6257',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '6301',
                    'stop' => '6377',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '6460',
                    'stop' => '6467',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '6470',
                    'stop' => '6477',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '7014',
                    'stop' => '7017',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '7020',
                    'stop' => '7027',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '7201',
                    'stop' => '7267',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '7270',
                    'stop' => '7277',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '7301',
                    'stop' => '7327',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '7501',
                    'stop' => '7507',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '7536',
                    'stop' => '7537',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '7570',
                    'stop' => '7577',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '7601',
                    'stop' => '7617',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '7620',
                    'stop' => '7677',
                    'created_at' => Carbon::now(),
                ],
                [
                    'start' => '7701',
                    'stop' => '7775',
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
        DB::table('ccams_squawk_ranges')->delete();
    }
}
