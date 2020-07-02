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
                    'first' => '0201',
                    'last' => '0277',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '0301',
                    'last' => '0377',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '0470',
                    'last' => '0477',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '0501',
                    'last' => '0577',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '0730',
                    'last' => '0767',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '1070',
                    'last' => '1077',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '1140',
                    'last' => '1176',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '1410',
                    'last' => '1477',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '2001',
                    'last' => '2077',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '2150',
                    'last' => '2177',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '2201',
                    'last' => '2277',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '2701',
                    'last' => '2737',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '3201',
                    'last' => '3277',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '3370',
                    'last' => '3377',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '3401',
                    'last' => '3477',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '3510',
                    'last' => '3537',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '4215',
                    'last' => '4247',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '4430',
                    'last' => '4477',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '4610',
                    'last' => '4667',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '4701',
                    'last' => '4777',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '5013',
                    'last' => '5017',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '5201',
                    'last' => '5270',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '5401',
                    'last' => '5477',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '5660',
                    'last' => '5664',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '5565',
                    'last' => '5676',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '6201',
                    'last' => '6257',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '6301',
                    'last' => '6377',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '6460',
                    'last' => '6467',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '6470',
                    'last' => '6477',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '7014',
                    'last' => '7017',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '7020',
                    'last' => '7027',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '7201',
                    'last' => '7267',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '7270',
                    'last' => '7277',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '7301',
                    'last' => '7327',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '7501',
                    'last' => '7507',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '7536',
                    'last' => '7537',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '7570',
                    'last' => '7577',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '7601',
                    'last' => '7617',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '7620',
                    'last' => '7677',
                    'created_at' => Carbon::now(),
                ],
                [
                    'first' => '7701',
                    'last' => '7775',
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
