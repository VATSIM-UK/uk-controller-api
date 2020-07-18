<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddConspicuitySquawkData extends Migration
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
                'unit' => 'EGXE',
                'code' => '0401',
            ],
            [
                'unit' => 'EGBE',
                'code' => '0420',
            ],
            [
                'unit' => 'EGXZ',
                'code' => '0407',
            ],
            [
                'unit' => 'EGNR',
                'code' => '0430',
            ],
            [
                'unit' => 'EGNH',
                'code' => '0450',
                'rules' => [
                    [
                        'type' => 'SERVICE',
                        'rule' => 'BASIC',
                    ],
                ],
            ],
            [
                'unit' => 'EGNH',
                'code' => '0451',
                'rules' => [
                    [
                        'type' => 'SERVICE',
                        'rule' => 'PROCEDURAL',
                    ],
                ],
            ],
            [
                'unit' => 'EGLF',
                'code' => '0467',
            ],
            [
                'unit' => 'EGHQ',
                'code' => '1747',
            ],
            [
                'unit' => 'EGXC',
                'code' => '1777',
            ],
            [
                'unit' => 'EGYD',
                'code' => '2645',
            ],
            [
                'unit' => 'EGDM',
                'code' => '2650',
            ],
            [
                'unit' => 'EGNM',
                'code' => '2654',
            ],
            [
                'unit' => 'EGHO',
                'code' => '2660',
            ],
            [
                'unit' => 'EGVP',
                'code' => '2676',
            ],
            [
                'unit' => 'EGUB',
                'code' => '3624',
            ],
            [
                'unit' => 'EGTB',
                'code' => '3637',
            ],
            [
                'unit' => 'EGVO',
                'code' => '3646',
            ],
            [
                'unit' => 'EGNO',
                'code' => '3650',
            ],
            [
                'unit' => 'SOLENT',
                'code' => '3666',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGYM',
                'code' => '3667',
            ],
            [
                'unit' => 'EGVN',
                'code' => '3737',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGSH',
                'code' => '3707',
            ],
            [
                'unit' => 'EGXT',
                'code' => '3750',
            ],
            [
                'unit' => 'EGKA',
                'code' => '3762',
                'rules' => [
                    [
                        'type' => 'FLIGHT_RULES',
                        'rule' => 'IFR',
                    ],
                ],
            ],
            [
                'unit' => 'EGKA',
                'code' => '3763',
                'rules' => [
                    [
                        'type' => 'FLIGHT_RULES',
                        'rule' => 'VFR',
                    ],
                ],
            ],
            [
                'unit' => 'EGKR',
                'code' => '3767',
            ],
            [
                'unit' => 'EGNT',
                'code' => '3767',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGDY',
                'code' => '4357',
            ],
            [
                'unit' => 'EGUW',
                'code' => '4501',
            ],
            [
                'unit' => 'EGXU',
                'code' => '4506',
            ],
            [
                'unit' => 'EGPK',
                'code' => '4520',
            ],
            [
                'unit' => 'EGTK',
                'code' => '4520',
            ],
            [
                'unit' => 'EGXU',
                'code' => '4540',
            ],
            [
                'unit' => 'EGNX',
                'code' => '4571',
            ],
            [
                'unit' => 'EGMC',
                'code' => '4575',
            ],
            [
                'unit' => 'EGUO',
                'code' => '4576',
            ],
            [
                'unit' => 'EGNC',
                'code' => '4677',
            ],
            [
                'unit' => 'EGGW',
                'code' => '4677',
            ],
            [
                'unit' => 'EGGP',
                'code' => '5050',
            ],
            [
                'unit' => 'EGGD',
                'code' => '5070',
                'rules' => [
                    [
                        'type' => 'FLIGHT_RULES',
                        'rule' => 'VFR',
                    ],
                ],
            ],
            [
                'unit' => 'EGGD',
                'code' => '5071',
                'rules' => [
                    [
                        'type' => 'FLIGHT_RULES',
                        'rule' => 'VFR',
                    ],
                ],
            ],
            [
                'unit' => 'EGXY',
                'code' => '5070',
                'rules' => [
                    [
                        'type' => 'FLIGHT_RULES',
                        'rule' => 'VFR',
                    ],
                ],
            ],
            [
                'unit' => 'EGXY',
                'code' => '5071',
                'rules' => [
                    [
                        'type' => 'FLIGHT_RULES',
                        'rule' => 'VFR',
                    ],
                ],
            ],
            [
                'unit' => 'EGCN',
                'code' => '6160',
            ],
            [
                'unit' => 'EGSC',
                'code' => '6176',
                'rules' => [
                    [
                        'type' => 'FLIGHT_RULES',
                        'rule' => 'VFR',
                    ],
                ],
            ],
            [
                'unit' => 'EGSC',
                'code' => '6177',
                'rules' => [
                    [
                        'type' => 'FLIGHT_RULES',
                        'rule' => 'IFR',
                    ],
                ],
            ],
            [
                'unit' => 'EGPE',
                'code' => '6177',
            ],
            [
                'unit' => 'EGDR',
                'code' => '7030',
            ],
            [
                'unit' => 'EGKB',
                'code' => '7047',
            ],
            [
                'unit' => 'EGLC',
                'code' => '7057',
            ],
            [
                'unit' => 'EGMD',
                'code' => '7066',
                'rules' => [
                    [
                        'type' => 'FLIGHT_RULES',
                        'rule' => 'VFR',
                    ],
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGNV',
                'code' => '7067',
            ],
            [
                'unit' => 'EGMD',
                'code' => '7067',
                'rules' => [
                    [
                        'type' => 'FLIGHT_RULES',
                        'rule' => 'IFR',
                    ],
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGLW',
                'code' => '7077',
            ],
            [
                'unit' => 'EGCB',
                'code' => '7365',
            ],
            [
                'unit' => 'EGPN',
                'code' => '7376',
                'rules' => [
                    [
                        'type' => 'FLIGHT_RULES',
                        'rule' => 'VFR',
                    ],
                ],
            ],
            [
                'unit' => 'EGHH',
                'code' => '7377',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGQL',
                'code' => '7402',
            ],
            [
                'unit' => 'EGTC',
                'code' => '7417',
                'rules' => [
                    [
                        'type' => 'FLIGHT_RULES',
                        'rule' => 'IFR',
                    ],
                ],
            ],
            [
                'unit' => 'EGOS',
                'code' => '7420',
            ],
        ];

        foreach ($codeData as $index => $code) {
            $code['created_at'] = Carbon::now();
            $rules = $code['rules'] ?? [];
            unset($code['rules']);

            $codeId = DB::table('unit_conspicuity_squawk_codes')
                ->insertGetId($code);

            foreach ($rules as $rule) {
                DB::table('unit_conspicuity_squawk_rules')
                    ->insert(
                        [
                            'unit_conspicuity_squawk_code_id' => $codeId,
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
        DB::table('unit_conspicuity_squawk_codes')->delete();
    }
}
