<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddSpeedGroupData extends Migration
{
    const SPEED_GROUPS = [
        'EGKK' => [
            '1' => [
                'aircraft' => [
                    'DHC6',
                    'E110',
                ],
            ],
            '2' => [
                'aircraft' => [
                    'F27',
                    'F406',
                    'JS31',
                    'JS32',
                    'BE99',
                    'TBM7',
                    'TBM8',
                    'E120',
                ],
            ],
            '3' => [
                'aircraft' => [
                    'AT43',
                    'AT45',
                    'AT46',
                    'AT72',
                    'AT73',
                    'AT75',
                    'AT76',
                    'ATP',
                    'BE9L',
                    'BE9T',
                    'BE10',
                    'BE20',
                    'BE30',
                    'B350',
                    'C130',
                    'DH8A',
                    'DH8B',
                    'DH8C',
                    'F50',
                    'JS41',
                    'JS42',
                    'PC12',
                    'SF34',
                    'SW3',
                    'SW4',
                ],
            ],
            '4' => [
                'aircraft' => [
                    'A400M',
                    'B461',
                    'B462',
                    'B463',
                    'RJ70',
                    'RJ85',
                    'RJ1H',
                    'DH8D',
                    'D328',
                    'EA50',
                    'E50P',
                    'SB20',
                    'SB20',
                    'C25A',
                    'C25B',
                    'C25C',
                    'C500',
                    'C501',
                    'C510',
                    'C525',
                    'C550',
                    'C551',
                    'C560',
                    'C650',
                ]
            ],
            '5' => [
                'aircraft' => [
                    'C56X',
                    'C680',
                    'C750',
                ],
                'engines' => [
                    'Jet',
                ],
            ],
            '6' => [
                'aircraft' => [
                    'CONC'
                ],
            ],
        ],
        'EGLL' => [
            '0' => [
                'aircraft' => [
                    'BE9L',
                    'DHC6',
                    'E110',
                    'SH36',
                ],
            ],
            '1' => [
                'aircraft' => [
                    'AT43',
                    'AT44',
                    'AT45',
                    'AT72',
                    'AT73',
                    'AT75',
                    'AT76',
                    'BE20',
                    'BE30',
                    'DH8A',
                    'DH8B',
                    'DH8C',
                    'JS31',
                    'JS32',
                    'JS41',
                    'SF34',
                ],
            ],
            '2' => [
                'aircraft' => [
                    'B461',
                    'B462',
                    'B463',
                    'RJ70',
                    'RJ85',
                    'RJ1H',
                    'C25A',
                    'C25B',
                    'C25C',
                    'C500',
                    'C501',
                    'C510',
                    'C525',
                    'C550',
                    'C551',
                    'C560',
                    'C650',
                    'C56X',
                    'C680',
                    'C750',
                    'C130',
                    'D328',
                    'J328',
                    'L328',
                    'DH8D',
                    'SB20',
                ]
            ],
            '3' => [
                'engines' => [
                    'Jet',
                ],
            ],
            '4' => [
                'aircraft' => [
                    'CONC',
                ],
            ],
        ],
        'EGLC' => [
            '0' => [
                'engines' => [
                    'Jet',
                ],
            ],
            '1' => [
                'aircraft' => [
                    'B461',
                    'B462',
                    'B463',
                    'RJ70',
                    'RJ85',
                    'RJ1H',
                    'C500',
                    'C501',
                    'C550',
                    'C551',
                    'C525',
                    'ARJ',
                    'CL60',
                    'DH8D',
                    'E135',
                    'E145',
                    'SB20',
                ]
            ],
            '2' => [
                'aircraft' => [
                    'AT43',
                    'AT44',
                    'AT45',
                    'AT72',
                    'AT73',
                    'AT75',
                    'AT76',
                    'ATP',
                    'BE20',
                    'BE9L',
                    'B350',
                    'DH8A',
                    'DH8B',
                    'DH8C',
                    'DHC7',
                    'E120',
                    'F27',
                    'F50',
                    'G159',
                    'JS31',
                    'JS32',
                    'JS41',
                    'SF34',
                ]
            ],
            '3' => [
                'aircraft' => [
                    'BE58',
                    'BE99',
                    'C404',
                    'E110',
                    'PA23',
                    'PA31',
                    'PA34',
                ]
            ],
        ],
        'EGSS' => [
            '0' => [
                'aircraft' => [
                    'BE9L',
                    'DHC6',
                    'E110',
                    'SH36',
                ],
            ],
            '1' => [
                'aircraft' => [
                    'AT42',
                    'AT43',
                    'AT44',
                    'AT72',
                    'AT73',
                    'AT75',
                    'AT76',
                    'BE20',
                    'BE35',
                    'F27',
                    'F50',
                    'SF34',
                    'DH8A',
                    'DH8B',
                    'DH8C',
                    'JS31',
                    'JS32',
                    'JS41',
                ],
            ],
            '2' => [
                'aircraft' => [
                    'B461',
                    'B462',
                    'B463',
                    'RJ70',
                    'RJ85',
                    'RJ1H',
                    'C501',
                    'C551',
                    'D328',
                    'J328',
                    'DH8D',
                    'SB20',
                ],
            ],
            '3' => [
                'aircraft' => [
                    'C501',
                    'C551',
                    'J328',
                ],
                'engines' => [
                    'Jet'
                ],
            ],
            '4' => [
                'aircraft' => [
                    'CONC',
                ],
            ],
        ],
        'EGGW' => [
            '0' => [
                'aircraft' => [
                    'BE99',
                    'E110',
                    'SH33',
                    'SH36',
                    'C404',
                    'PA23',
                    'PA31',
                ],
            ],
            '1' => [
                'aircraft' => [
                    'ATP',
                    'AT43',
                    'AT45',
                    'AT72',
                    'BE20',
                    'DHC6',
                    'DHC7',
                    'DH8A',
                    'DH8B',
                    'DH8C',
                    'DH8D',
                    'E120',
                    'F27',
                    'F50',
                    'G159',
                    'JS31',
                    'JS32',
                    'JS41',
                    'SF34',
                ],
            ],
            '2' => [
                'B461',
                'B462',
                'B463',
                'RJ70',
                'RJ85',
                'RJ1H',
                'C501',
                'C551',
                'CARJ',
                'CL60',
                'E145',
            ],
            '3' => [
                'engines' => [
                    'Jet',
                ],
            ],
            '4' => [
                'aircraft' => [
                    'CONC',
                ],
            ],
        ],
        'EGCC' => [
            '0' => [
                'aircraft' => [
                    'BE9L',
                    'DHC6',
                    'E110',
                    'SH36',
                ]
            ],
            '1' => [
                'aircraft' => [
                    'ATP',
                    'AT43',
                    'AT44',
                    'AT72',
                    'BE20',
                    'BE30',
                    'DH8A',
                    'DH8B',
                    'DH8C',
                    'F27',
                    'F50',
                    'JS31',
                    'JS32',
                    'JS41',
                    'SF34',
                ],
            ],
            '2' => [
                'aircraft' => [
                    'B461',
                    'B462',
                    'B463',
                    'RJ70',
                    'RJ85',
                    'RJ1H',
                    'C25A',
                    'C25B',
                    'C25C',
                    'C500',
                    'C501',
                    'C510',
                    'C525',
                    'C550',
                    'C551',
                    'C560',
                    'C650',
                    'C56X',
                    'C680',
                    'C750',
                    'C130',
                    'D328',
                    'J328',
                    'L328',
                    'DH8D',
                    'SB20',
                ],
            ],
            '3' => [
                'engines' => [
                    'Jet'
                ],
            ],
            '4' => [
                'aircraft' => [
                    'CONC',
                ]
            ],
        ],
        'EGGP' => [
            '0' => [
                'aircraft' => [
                    'BE9L',
                    'DHC6',
                    'E110',
                    'SH36',
                ],
            ],
            '1' => [
                'aircraft' => [
                    'AT43',
                    'AT44',
                    'AT72',
                    'BE20',
                    'BE35',
                    'F27',
                    'F50',
                    'SF34',
                    'DH8A',
                    'DH8B',
                    'DH8C',
                    'JS31',
                    'JS32',
                    'JS41',
                ],
            ],
            '2' => [
                'B461',
                'B462',
                'B463',
                'RJ70',
                'RJ85',
                'RJ1H',
                'C501',
                'C551',
                'D328',
                'J328',
                'DH8D',
                'SB20',
            ],
            '3' => [
                'engines' => [
                    'Jet'
                ],
            ],
            '4' => [
                'aircraft' => [
                    'CONC',
                ],
            ],
        ],
        'EGGD' => [
            '1' => [
                'aircraft' => [
                    'E110',
                    'C404',
                    'PA31',
                    'DHC6',
                    'DA42',
                    'SH36',
                    'PA23',
                    'PA34',
                    'BE76',
                    'PA32',
                ],
            ],
            '2' => [
                'aircraft' => [
                    'ATP',
                    'DHC7',
                    'C431',
                    'AT43',
                    'AT44',
                    'AT45',
                    'AT72',
                    'AT73',
                    'AT75',
                    'AT76',
                    'DH8A',
                    'DH8B',
                    'DH8C',
                    'JS31',
                    'JS32',
                    'JS41',
                    'SW3',
                    'SW4',
                ],
            ],
            '3' => [
                'aircraft' => [
                    'B461',
                    'B462',
                    'B463',
                    'RJ70',
                    'RJ85',
                    'RJ1H',
                    'E135',
                    'E145',
                    'SB20',
                    'RJ80',
                    'C500',
                    'C501',
                    'C550',
                    'C551',
                    'D328J',
                    'CL60',
                    'C525',
                    'C130',
                    'DH8D',
                    'CRJ1',
                    'CRJ2',
                    'CRJ7',
                    'CRJ9',
                    'L188',
                ]
            ],
            '4' => [
                'engines' => [
                    'Jet'
                ],
            ],
        ],
        'EGFF' => [
            '1' => [
                'aircraft' => [
                    'DHC6',
                    'L410',
                ],
            ],
            '2' => [
                'aircraft' => [
                    'F27',
                    'F406',
                    'JS31',
                    'JS32',
                    'BE99',
                    'TBM7',
                    'TBM8',
                    'E120',
                ],
            ],
            '3' => [
                'aircraft' => [
                    'AT42',
                    'AT43',
                    'AT45',
                    'AT72',
                    'AT73',
                    'AT75',
                    'AT76',
                    'ATP',
                    'BE9L',
                    'BE9T',
                    'BE10',
                    'BE20',
                    'BE30',
                    'B350',
                    'C130',
                    'DH8A',
                    'DH8B',
                    'DH8C',
                    'DHC7',
                    'F50',
                    'JS41',
                    'PC12',
                    'SF34',
                    'SW3',
                    'SW4',
                ],
            ],
            '4' => [
                'aircraft' => [
                    'A400',
                    'B461',
                    'B462',
                    'B463',
                    'RJ70',
                    'RJ85',
                    'RJ1H',
                    'BE40',
                    'D328',
                    'DH8D',
                    'EA50',
                    'E50P',
                    'E55P',
                    'E135',
                    'E145',
                    'SB20',
                    'SB20',
                    'C25A',
                    'C25B',
                    'C25C',
                    'C500',
                    'C501',
                    'C510',
                    'C525',
                    'C550',
                    'C551',
                    'C560',
                    'C650',
                ],
            ],
            '5' => [
                'engines' => [
                    'Jet',
                ],
            ],
            '6' => [
                'aircraft' => [
                    'CONC',
                ]
            ],
        ],
        'EGPH' => [
            '1' => [
                'aircraft' => [
                    'DHC6',
                    'L410',
                ]
            ],
            '2' => [
                'aircraft' => [
                    'F27',
                    'F406',
                    'JS31',
                    'JS32',
                    'BE99',
                    'TBM7',
                    'TBM8',
                    'E120',
                ]
            ],
            '3' => [
                'aircraft' => [
                    'AT42',
                    'AT43',
                    'AT45',
                    'AT72',
                    'AT75',
                    'AT76',
                    'ATP',
                    'BE9L',
                    'BE9T',
                    'BE10',
                    'BE20',
                    'BE30',
                    'B350',
                    'C130',
                    'DH8A',
                    'DH8B',
                    'DH8C',
                    'F50',
                    'JS41',
                    'PC12',
                    'SF34',
                    'SW3',
                    'SW4',
                ]
            ],
            '4' => [
                'aircraft' => [
                    'A400',
                    'L410',
                    'B461',
                    'B462',
                    'B463',
                    'RJ70',
                    'RJ85',
                    'RJ1H',
                    'DH8D',
                    'D328',
                    'EA50',
                    'SB20',
                    'C25A',
                    'C25B',
                    'C25C',
                    'C500',
                    'C501',
                    'C510',
                    'C525',
                    'C550',
                    'C551',
                    'C560',
                    'C650',
                ]
            ],
            '5' => [
                'engines' => [
                    'Jet',
                ],
            ],
            '6' => [
                'aircraft' => [
                    'CONC',
                ],
            ],
        ],
        'EGPF' => [
            '0' => [
                'aircraft' => [
                    'BE99',
                    'E110',
                    'SH33',
                    'SH36',
                    'C404',
                    'PA23',
                    'PA31',
                ],
            ],
            '1' => [
                'aircraft' => [
                    'ATP',
                    'AT43',
                    'AT44',
                    'AT45',
                    'AT72',
                    'AT75',
                    'AT76',
                    'BE20',
                    'DHC6',
                    'DHC7',
                    'DH8A',
                    'DH8B',
                    'DH8C',
                    'E120',
                    'F27',
                    'F50',
                    'G159',
                    'JS31',
                    'JS32',
                    'JS41',
                    'SF34',
                ],
            ],
            '2' => [
                'aircraft' => [
                    'B461',
                    'B462',
                    'B463',
                    'RJ70',
                    'RJ85',
                    'RJ1H',
                    'CARJ',
                    'CL60',
                    'E145',
                ],
            ],
            '3' => [
                'engines' => [
                    'Jet',
                ],
            ],
            '4' => [
                'aircraft' => [
                    'CONC',
                ],
            ],
        ]
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $airfields = DB::table('airfield')->get()->mapWithKeys(
            function ($airfield) {
                return [$airfield->code => $airfield->id];
            }
        )->toArray();

        foreach (self::SPEED_GROUPS as $airfield => $speedGroups) {
            foreach ($speedGroups as $key => $group) {
                $groupId = DB::table('speed_groups')->insertGetId(
                    [
                        'airfield_id' => $airfields[$airfield],
                        'key' => sprintf('%s_GROUP_%d', $airfield, $key),
                        'created_at' => Carbon::now(),
                    ]
                );

                if (isset($group['aircraft'])) {
                    $aircraftToInclude = DB::table('aircraft')->whereIn('code', $group['aircraft'])
                        ->pluck('id');

                    $aircraftToInclude = $aircraftToInclude->map(
                        function ($aircraftId) use ($groupId) {
                            return [
                                'aircraft_id' => $aircraftId,
                                'speed_group_id' => $groupId,
                            ];
                        }
                    );

                    DB::table('aircraft_speed_group')->insert($aircraftToInclude->toArray());
                }

                if (isset($group['engines'])) {
                    DB::table('engine_type_speed_group')->insert(
                        [
                            'engine_type_id' => DB::table('engine_types')->where('type', $group['engines'])->first(
                            )->id,
                            'speed_group_id' => $groupId,
                            'created_at' => Carbon::now(),
                        ]
                    );
                }
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
        // Nothing to do
    }
}
