<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitDiscreteSquawkCodeData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $codeData = [
            [
                'unit' => 'EGBB_APP',
                'first' => '0401',
                'last' => '0417',
            ],
            [
                'unit' => 'EGTE_N_APP',
                'first' => '0401',
                'last' => '0450',
            ],
            [
                'unit' => 'EGTE_S_APP',
                'first' => '0401',
                'last' => '0450',
            ],
            [
                'unit' => 'EGUL',
                'first' => '0401',
                'last' => '0467',
            ],
            [
                'unit' => 'EGXE',
                'first' => '0402',
                'last' => '0426',
            ],
            [
                'unit' => 'EGLF',
                'first' => '0421',
                'last' => '0427',
                'rules' => [[
                    'type' => 'FLIGHT_RULES',
                    'rule' => 'VFR',
                ]],
            ],
        ];

        foreach ($codeData as $index => $code) {
            $code['created_at'] = Carbon::now();
            $rules = $code['rules'] ?? [];
            unset($code['rules']);

            $codeId = DB::table('unit_discrete_squawk_codes')
                ->insertGetId($code);

            foreach ($rules as $rule) {
                DB::table('unit_discrete_squawk_code_rules')
                    ->insert(
                        [
                            'unit_discrete_squawk_code_id' => $codeId,
                            'rule' => json_encode($rule),
                            'created_at' => Carbon::now(),
                        ]
                    );
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('unit_discrete_squawk_codes')->delete();
    }
}
