<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

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
                'unit' => 'EGBB',
                'first' => '0401',
                'last' => '0417',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGTE',
                'first' => '0401',
                'last' => '0450',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGTE',
                'first' => '0401',
                'last' => '0450',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
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
                'rules' => [
                    [
                        'type' => 'FLIGHT_RULES',
                        'rule' => 'IFR',
                    ],
                ],
            ],
            [
                'unit' => 'EGPH',
                'first' => '0430',
                'last' => '0437',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGNR',
                'first' => '0431',
                'last' => '0446',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGPH',
                'first' => '0441',
                'last' => '0443',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGLF',
                'first' => '0460',
                'last' => '0466',
                'rules' => [
                    [
                        'type' => 'FLIGHT_RULES',
                        'rule' => 'VFR',
                    ],
                ],
            ],
            [
                'unit' => 'EGHQ',
                'first' => '1730',
                'last' => '1744',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGXC',
                'first' => '1730',
                'last' => '1776',
            ],
            [
                'unit' => 'EGLF',
                'first' => '1750',
                'last' => '1757',
                'rules' => [
                    [
                        'type' => 'FLIGHT_RULES',
                        'rule' => 'IFR',
                    ],
                ],
            ],
            [
                'unit' => 'EGHQ',
                'first' => '1750',
                'last' => '1757',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGPM',
                'first' => '1750',
                'last' => '1757',
            ],
            [
                'unit' => 'EGDY',
                'first' => '1760',
                'last' => '1757',
            ],
            [
                'unit' => 'EGPF',
                'first' => '2601',
                'last' => '2617',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGYD',
                'first' => '2601',
                'last' => '2637',
            ],
            [
                'unit' => 'EGDM',
                'first' => '2601',
                'last' => '2645',
            ],
            [
                'unit' => 'EGPB',
                'first' => '2621',
                'last' => '2630',
            ],
            [
                'unit' => 'EGPB',
                'first' => '2621',
                'last' => '2630',
            ],
            [
                'unit' => 'EGPD',
                'first' => '2631',
                'last' => '2637',
            ],
            [
                'unit' => 'EGPB',
                'first' => '2640',
                'last' => '2657',
            ],
            [
                'unit' => 'EGYD',
                'first' => '2643',
                'last' => '2644',
            ],
            [
                'unit' => 'EGYD',
                'first' => '2646',
                'last' => '2647',
            ],
            [
                'unit' => 'EGNM',
                'first' => '2655',
                'last' => '2676',
            ],
            [
                'unit' => 'EGPD',
                'first' => '2660',
                'last' => '2677',
            ],
            [
                'unit' => 'EGVP',
                'first' => '2661',
                'last' => '2675',
            ],
            [
                'unit' => 'EGVV',
                'first' => '3310',
                'last' => '3367',
            ],
            [
                'unit' => 'SCO',
                'first' => '3601',
                'last' => '3632',
            ],
            [
                'unit' => 'EGUB',
                'first' => '3601',
                'last' => '3623',
            ],
            [
                'unit' => 'EGXW',
                'first' => '3601',
                'last' => '3634',
            ],
            [
                'unit' => 'EGUB',
                'first' => '3601',
                'last' => '3623',
            ],
            [
                'unit' => 'EGJJ',
                'first' => '3601',
                'last' => '3647',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGFF',
                'first' => '3601',
                'last' => '3657',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGUB',
                'first' => '3625',
                'last' => '3627',
            ],
            [
                'unit' => 'EGVO',
                'first' => '3640',
                'last' => '3645',
            ],
            [
                'unit' => 'EGYM',
                'first' => '3640',
                'last' => '3666',
            ],
            [
                'unit' => 'EGPD',
                'first' => '3640',
                'last' => '3677',
            ],
            [
                'unit' => 'EGNO',
                'first' => '3641',
                'last' => '3647',
            ],
            [
                'unit' => 'EGVO',
                'first' => '3647',
                'last' => '3653',
            ],
            [
                'unit' => 'EGNO',
                'first' => '3651',
                'last' => '3657',
            ],
            [
                'unit' => 'SOLENT',
                'first' => '3660',
                'last' => '3665',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGNO',
                'first' => '3661',
                'last' => '3677',
            ],
            [
                'unit' => 'EGHI',
                'first' => '3667',
                'last' => '3677',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'SOLENT',
                'first' => '3667',
                'last' => '3677',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGSH',
                'first' => '3701',
                'last' => '3706',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGSH',
                'first' => '3701',
                'last' => '3710',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGVN',
                'first' => '3701',
                'last' => '3736',
            ],
            [
                'unit' => 'EGJB',
                'first' => '3701',
                'last' => '3747',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGVN',
                'first' => '3701',
                'last' => '3736',
            ],
            [
                'unit' => 'EGQS',
                'first' => '3701',
                'last' => '3767',
            ],
            [
                'unit' => 'EGOV',
                'first' => '3720',
                'last' => '3727',
            ],
            [
                'unit' => 'EGXT',
                'first' => '3720',
                'last' => '3727',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGNT',
                'first' => '3720',
                'last' => '3766',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGOV',
                'first' => '3730',
                'last' => '3736',
            ],
            [
                'unit' => 'EGXT',
                'first' => '3730',
                'last' => '3747',
            ],
            [
                'unit' => 'EGVN',
                'first' => '3740',
                'last' => '3745',
            ],
            [
                'unit' => 'EGOV',
                'first' => '3740',
                'last' => '3747',
            ],
            [
                'unit' => 'EGOV',
                'first' => '3750',
                'last' => '3751',
            ],
            [
                'unit' => 'EGKK',
                'first' => '3750',
                'last' => '3761',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGXT',
                'first' => '3760',
                'last' => '3765',
            ],
            [
                'unit' => 'EGKK',
                'first' => '3764',
                'last' => '3766',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'TWR',
                    ],
                ],
            ],
            [
                'unit' => 'EGAC',
                'first' => '4250',
                'last' => '4257',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGPD',
                'first' => '4250',
                'last' => '4267',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGNJ',
                'first' => '4250',
                'last' => '4277',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'LON',
                'first' => '4307',
                'last' => '4317',
            ],
            [
                'unit' => 'EGYD',
                'first' => '4320',
                'last' => '4327',
            ],
            [
                'unit' => 'SCO',
                'first' => '4330',
                'last' => '4337',
            ],
            [
                'unit' => 'EGWU',
                'first' => '4360',
                'last' => '4367',
            ],
            [
                'unit' => 'EGYD',
                'first' => '4370',
                'last' => '4377',
            ],
            [
                'unit' => 'EGXU',
                'first' => '4501',
                'last' => '4505',
            ],
            [
                'unit' => 'EGTK',
                'first' => '4501',
                'last' => '4516',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGPK',
                'first' => '4501',
                'last' => '4517',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGUW',
                'first' => '4502',
                'last' => '4547',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGXU',
                'first' => '4507',
                'last' => '4527',
            ],
            [
                'unit' => 'EGXU',
                'first' => '4531',
                'last' => '4537',
            ],
            [
                'unit' => 'EGFA',
                'first' => '4531',
                'last' => '4537',
            ],
            [
                'unit' => 'EGFA',
                'first' => '4540',
                'last' => '4542',
            ],
            [
                'unit' => 'EGXU',
                'first' => '4541',
                'last' => '4547',
            ],
            [
                'unit' => 'EGNS',
                'first' => '4550',
                'last' => '4567',
            ],
            [
                'unit' => 'EGNX',
                'first' => '4550',
                'last' => '4570',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'ESSEX',
                'first' => '4670',
                'last' => '4676',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGGW',
                'first' => '4670',
                'last' => '4676',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGSS',
                'first' => '4670',
                'last' => '4676',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'LON',
                'first' => '5001',
                'last' => '5012',
            ],
            [
                'unit' => 'EGLF',
                'first' => '5020',
                'last' => '5036',
            ],
            [
                'unit' => 'EGGD',
                'first' => '5050',
                'last' => '5057',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGMC',
                'first' => '5050',
                'last' => '5057',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGGP',
                'first' => '5050',
                'last' => '5057',
            ],
            [
                'unit' => 'EGGD',
                'first' => '5072',
                'last' => '5076',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGVV',
                'first' => '6040',
                'last' => '6077',
            ],
            [
                'unit' => 'EGVV',
                'first' => '6101',
                'last' => '6157',
            ],
            [
                'unit' => 'EGSC',
                'first' => '6160',
                'last' => '6175',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGPE',
                'first' => '6160',
                'last' => '6176',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGCN',
                'first' => '6161',
                'last' => '6167',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGCN',
                'first' => '6171',
                'last' => '6177',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGVV',
                'first' => '6401',
                'last' => '6457',
            ],
            [
                'unit' => 'EGAA',
                'first' => '7030',
                'last' => '7044',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'THAMES',
                'first' => '7030',
                'last' => '7046',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGLL',
                'first' => '7030',
                'last' => '7046',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGNV',
                'first' => '7030',
                'last' => '7066',
            ],
            [
                'unit' => 'EGDR',
                'first' => '7031',
                'last' => '7077',
            ],
            [
                'unit' => 'EGAA',
                'first' => '7046',
                'last' => '7047',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'THAMES',
                'first' => '7050',
                'last' => '7056',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGLL',
                'first' => '7050',
                'last' => '7056',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'THAMES',
                'first' => '7070',
                'last' => '7076',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGLL',
                'first' => '7070',
                'last' => '7076',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGCC',
                'first' => '7350',
                'last' => '7364',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGHH',
                'first' => '7350',
                'last' => '7376',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGSH',
                'first' => '7351',
                'last' => '7377',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGCC',
                'first' => '7367',
                'last' => '7373',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'ESSEX',
                'first' => '7402',
                'last' => '7414',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGGW',
                'first' => '7402',
                'last' => '7414',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGSS',
                'first' => '7402',
                'last' => '7414',
                'rules' => [
                    [
                        'type' => 'UNIT_TYPE',
                        'rule' => 'APP',
                    ],
                ],
            ],
            [
                'unit' => 'EGOS',
                'first' => '7402',
                'last' => '7417',
            ],
            [
                'unit' => 'EGQL',
                'first' => '7403',
                'last' => '7417',
            ],
            [
                'unit' => 'EGOS',
                'first' => '7421',
                'last' => '7425',
            ],
            [
                'unit' => 'EGOS',
                'first' => '7430',
                'last' => '7437',
            ],
        ];

        foreach ($codeData as $index => $code) {
            $code['created_at'] = Carbon::now();
            $rules = $code['rules'] ?? [];
            unset($code['rules']);

            $codeId = DB::table('unit_discrete_squawk_ranges')
                ->insertGetId($code);

            foreach ($rules as $rule) {
                DB::table('unit_discrete_squawk_range_rules')
                    ->insert(
                        [
                            'unit_discrete_squawk_range_id' => $codeId,
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
        DB::table('unit_discrete_squawk_ranges')->delete();
    }
}
