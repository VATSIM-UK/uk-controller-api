<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddOrcamRangeData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('orcam_squawk_ranges')
            ->insert(
                [
                    [
                        'origin' => 'EB',
                        'first' => '0101',
                        'last' => '0117',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'ED',
                        'first' => '0120',
                        'last' => '0137',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'EH',
                        'first' => '0140',
                        'last' => '0177',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'ED',
                        'first' => '0601',
                        'last' => '0637',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'LFP',
                        'first' => '0640',
                        'last' => '0677',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'LE',
                        'first' => '1001',
                        'last' => '1067',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'ED',
                        'first' => '1101',
                        'last' => '1137',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'ED',
                        'first' => '1330',
                        'last' => '1377',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'EH',
                        'first' => '2101',
                        'last' => '2147',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'LF',
                        'first' => '2301',
                        'last' => '2377',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'ED',
                        'first' => '2501',
                        'last' => '2577',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'LS',
                        'first' => '3001',
                        'last' => '3077',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'ED',
                        'first' => '3101',
                        'last' => '3177',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'ED',
                        'first' => '3201',
                        'last' => '3277',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'EL',
                        'first' => '3501',
                        'last' => '3507',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'EDD',
                        'first' => '3540',
                        'last' => '3577',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'LF',
                        'first' => '4001',
                        'last' => '4077',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'ED',
                        'first' => '4101',
                        'last' => '4177',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'EB',
                        'first' => '4401',
                        'last' => '4427',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'EGJ',
                        'first' => '5271',
                        'last' => '5277',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'LEB',
                        'first' => '5301',
                        'last' => '5377',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'LEB',
                        'first' => '5501',
                        'last' => '5577',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'LFP',
                        'first' => '5601',
                        'last' => '5647',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'EL',
                        'first' => '5650',
                        'last' => '5657',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'LSG',
                        'first' => '5701',
                        'last' => '5777',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'EH',
                        'first' => '6260',
                        'last' => '6277',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'ED',
                        'first' => '6601',
                        'last' => '6677',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'LF',
                        'first' => '6701',
                        'last' => '6777',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'EB',
                        'first' => '7101',
                        'last' => '7167',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'EL',
                        'first' => '7170',
                        'last' => '7177',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'EH',
                        'first' => '7330',
                        'last' => '7347',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'LF',
                        'first' => '7440',
                        'last' => '7477',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'LS',
                        'first' => '7510',
                        'last' => '7535',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'ED',
                        'first' => '7540',
                        'last' => '7547',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'origin' => 'LFP',
                        'first' => '7550',
                        'last' => '7567',
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
        DB::table('orcam_squawk_ranges')->truncate();
    }
}
