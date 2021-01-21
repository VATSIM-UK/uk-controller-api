<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddSpeedGroupData extends Migration
{

    const SPEED_GROUPS = [
        'EGKK' => [
            'EGKK_GROUP_1' => [
                'aircraft' =>  [
                    'DHC6',
                    'E110',
                ],
            ],
            'EGKK_GROUP_2' => [
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
            'EGKK_GROUP_3' => [
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
            'EGKK_GROUP_4' => [
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
                    'C510',
                    'C525',
                    'C550',
                    'C560',
                    'C650',
                ]
            ],
            'EGKK_GROUP_5' => [
                'aircraft' => [
                    'C56X',
                    'C680',
                    'C750',
                ],
                'engines' => [
                    'Jet',
                ],
            ],
            'EGKK_GROUP_6' => [
                'aircraft' => [
                    'CONC'
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
        $airfields = DB::table('airfield')->get()->mapWithKeys(function ($airfield) {
            return [$airfield->code => $airfield->id];
        })->toArray();

        foreach (self::SPEED_GROUPS as $airfield => $speedGroups)
        {
            foreach ($speedGroups as $key => $group) {
                $groupId = DB::table('speed_groups')->insertGetId(
                    [
                        'airfield_id' => $airfields[$airfield],
                        'key' => $key,
                        'created_at' => Carbon::now(),
                    ]
                );

                if (isset($group['aircraft'])) {
                    $aircraftToInclude = DB::table('aircraft')->whereIn('code', $group['aircraft'])
                        ->pluck('id');

                    $aircraftToInclude = $aircraftToInclude->map(function ($aircraftId) use ($groupId) {
                        return [
                            'aircraft_id' => $aircraftId,
                            'speed_group_id' => $groupId,
                        ];
                    });

                    DB::table('aircraft_speed_group')->insert($aircraftToInclude->toArray());
                }

                if (isset($group['engines'])) {
                    DB::table('engine_type_speed_group')->insert(
                        [
                            'engine_type_id' => DB::table('engine_types')->where('type', $group['engines'])->first()->id,
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
