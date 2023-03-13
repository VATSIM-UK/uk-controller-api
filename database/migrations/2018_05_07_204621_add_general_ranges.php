<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddGeneralRanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rangeInfo = [
            // ORCAM Stuff
            [
                'departure_ident' => 'EB',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '0101',
                'stop' => '0177',
            ],
            [
                'departure_ident' => 'ED',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '0120',
                'stop' => '0137',
            ],
            [
                'departure_ident' => 'EH',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '0140',
                'stop' => '0177',
            ],
            [
                'departure_ident' => 'ED',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '0601',
                'stop' => '0637',
            ],
            [
                'departure_ident' => 'L',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '0640',
                'stop' => '0677',
            ],
            [
                'departure_ident' => 'ED',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '0701',
                'stop' => '0777',
            ],
            [
                'departure_ident' => 'EH',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '0701',
                'stop' => '0777',
            ],
            [
                'departure_ident' => 'EB',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '0701',
                'stop' => '0777',
            ],
            [
                'departure_ident' => 'LE',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '1001',
                'stop' => '1067',
            ],
            [
                'departure_ident' => 'LE',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '1140',
                'stop' => '1157',
            ],
            [
                'departure_ident' => 'LP',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '1001',
                'stop' => '1067',
            ],
            [
                'departure_ident' => 'LP',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '1140',
                'stop' => '1157',
            ],
            [
                'departure_ident' => 'ED',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '1330',
                'stop' => '1377',
            ],
            [
                'departure_ident' => 'EH',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '2101',
                'stop' => '2177',
            ],
            [
                'departure_ident' => 'LF',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '2301',
                'stop' => '2377',
            ],
            [
                'departure_ident' => 'ED',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '2501',
                'stop' => '2577',
            ],
            [
                'departure_ident' => 'EI',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '2601',
                'stop' => '2657',
            ],
            [
                'departure_ident' => 'LS',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '2740',
                'stop' => '2777',
            ],
            [
                'departure_ident' => 'LS',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '3001',
                'stop' => '3077',
            ],
            [
                'departure_ident' => 'ED',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '3101',
                'stop' => '3127',
            ],
            [
                'departure_ident' => 'LT',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '3201',
                'stop' => '3277',
            ],
            [
                'departure_ident' => 'EL',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '3501',
                'stop' => '3577',
            ],
            [
                'departure_ident' => 'ED',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '3540',
                'stop' => '3577',
            ],
            [
                'departure_ident' => 'LF',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '4001',
                'stop' => '4077',
            ],
            [
                'departure_ident' => 'ED',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '4101',
                'stop' => '4177',
            ],
            [
                'departure_ident' => 'LE',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '5301',
                'stop' => '5377',
            ],
            [
                'departure_ident' => 'LP',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '5301',
                'stop' => '5377',
            ],
            [
                'departure_ident' => 'LE',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '5501',
                'stop' => '5577',
            ],
            [
                'departure_ident' => 'LP',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '5501',
                'stop' => '5577',
            ],
            [
                'departure_ident' => 'LF',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '5601',
                'stop' => '5647',
            ],
            [
                'departure_ident' => 'EL',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '5650',
                'stop' => '5677',
            ],
            [
                'departure_ident' => 'LS',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '5701',
                'stop' => '5777',
            ],
            [
                'departure_ident' => 'EH',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '6260',
                'stop' => '6277',
            ],
            [
                'departure_ident' => 'ED',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '6601',
                'stop' => '6677',
            ],
            [
                'departure_ident' => 'ED',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '6701',
                'stop' => '6777',
            ],
            [
                'departure_ident' => 'EB',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '7101',
                'stop' => '7177',
            ],
            [
                'departure_ident' => 'EH',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '7330',
                'stop' => '7347',
            ],
            [
                'departure_ident' => 'LF',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '7440',
                'stop' => '7477',
            ],
            [
                'departure_ident' => 'LS',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '7510',
                'stop' => '7535',
            ],
            [
                'departure_ident' => 'ED',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '7540',
                'stop' => '7547',
            ],
            [
                'departure_ident' => 'LF',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '7550',
                'stop' => '7567',
            ],
            // CI Has ORCAM Too...
            [
                'departure_ident' => 'EGJJ',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '5271',
                'stop' => '5277',
            ],
            [
                'departure_ident' => 'EGJB',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '5271',
                'stop' => '5277',
            ],
            [
                'departure_ident' => 'EGJA',
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => '5271',
                'stop' => '5277',
            ],

            // CHANNEL ISLANDS - Have special codes to the UK
            [
                'departure_ident' => 'EGJJ',
                'arrival_ident' => 'EG',
                'rules' => 'A',
                'start' => '1201',
                'stop' => '1277',
            ],
            [
                'departure_ident' => 'EGJB',
                'arrival_ident' => 'EG',
                'rules' => 'A',
                'start' => '1201',
                'stop' => '1277',
            ],
            [
                'departure_ident' => 'EGJA',
                'arrival_ident' => 'EG',
                'rules' => 'A',
                'start' => '1201',
                'stop' => '1277',
            ],
            [
                'departure_ident' => 'EGJJ',
                'arrival_ident' => 'EGJB',
                'rules' => 'A',
                'start' => '3710',
                'stop' => '3727',
            ],
            [
                'departure_ident' => 'EGJJ',
                'arrival_ident' => 'EGJA',
                'rules' => 'A',
                'start' => '3710',
                'stop' => '3727',
            ],
            [
                'departure_ident' => 'EGJB',
                'arrival_ident' => 'EGJA',
                'rules' => 'A',
                'start' => '3730',
                'stop' => '3747',
            ],
            [
                'departure_ident' => 'EGJB',
                'arrival_ident' => 'EGJJ',
                'rules' => 'A',
                'start' => '3730',
                'stop' => '3747',
            ],
            [
                'departure_ident' => 'EGJA',
                'arrival_ident' => 'EGJB',
                'rules' => 'A',
                'start' => '3730',
                'stop' => '3747',
            ],
            [
                'departure_ident' => 'EGJA',
                'arrival_ident' => 'EGJJ',
                'rules' => 'A',
                'start' => '3730',
                'stop' => '3747',
            ],


            // CCAMS - ala, the last resort
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '0201',
                'stop' => '0277',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '0301',
                'stop' => '0377',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '0470',
                'stop' => '0477',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '0501',
                'stop' => '0577',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '0730',
                'stop' => '0767',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '1070',
                'stop' => '1077',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '1160',
                'stop' => '1176',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '1410',
                'stop' => '1477',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '2001',
                'stop' => '2077',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '2201',
                'stop' => '2277',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '2701',
                'stop' => '2737',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '3201',
                'stop' => '3277',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '3370',
                'stop' => '3377',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '3401',
                'stop' => '3477',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '3510',
                'stop' => '3537',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '4215',
                'stop' => '4247',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '4430',
                'stop' => '4477',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '4701',
                'stop' => '4777',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '5013',
                'stop' => '5017',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '5201',
                'stop' => '5270',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '5401',
                'stop' => '5477',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '5660',
                'stop' => '5664',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '5665',
                'stop' => '5677',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '6201',
                'stop' => '6257',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '6301',
                'stop' => '6377',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '6470',
                'stop' => '6477',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '7014',
                'stop' => '7017',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '7020',
                'stop' => '7027',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '7201',
                'stop' => '7267',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '7270',
                'stop' => '7277',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '7301',
                'stop' => '7327',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '7501',
                'stop' => '7507',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '7536',
                'stop' => '7537',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '7570',
                'stop' => '7577',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '7601',
                'stop' => '7617',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '7620',
                'stop' => '7677',
            ],
            [
                'departure_ident' => 'CCAMS',
                'arrival_ident' => 'CCAMS',
                'rules' => 'A',
                'start' => '7701',
                'stop' => '7775',
            ],
        ];

        // We only need one range owner for all of the general squawks.
        $processedOwners = [];

        // Process the ranges
        foreach ($rangeInfo as $range) {
            // If we don't yet have range owner, create one. There should be one range owner per combination of arr/dep
            if (!isset($processedOwners[$range['departure_ident'] . '|' . $range['arrival_ident']])) {
                // Create the range owner and range information
                $processedOwners[$range['departure_ident'] . '|' . $range['arrival_ident']] =
                    DB::table('squawk_general')->insertGetId(
                        [
                            'departure_ident' => $range['departure_ident'],
                            'arrival_ident' => $range['arrival_ident'],
                            'squawk_range_owner_id' => DB::table('squawk_range_owner')->insertGetId([]),
                        ]
                    );
            }

            // Create the range
            DB::table('squawk_range')->insert(
                [
                    'start' => $range['start'],
                    'stop' => $range['stop'],
                    'rules' => $range['rules'],
                    'allow_duplicate' => false,
                    'squawk_range_owner_id' => $processedOwners[
                        $range['departure_ident'] . '|' . $range['arrival_ident']
                    ],
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('squawk_general')->select(['id', 'squawk_range_owner_id'])->orderByDesc('id')->each(function ($value) {
            DB::table('squawk_general')->where('id', $value->id)->delete();
            DB::table('squawk_range_owner')->where('id', $value->squawk_range_owner_id)->delete();
        });
    }
}
