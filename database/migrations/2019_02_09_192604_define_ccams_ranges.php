<?php

use Illuminate\Database\Migrations\Migration;

class DefineCcamsRanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rangeInfo = [
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => null,
                    'rules' => 'A',
                    'start' => '0301',
                    'stop' => '0377',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EG',
                    'rules' => 'A',
                    'start' => '0470',
                    'stop' => '0477',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => null,
                    'rules' => 'A',
                    'start' => '0501',
                    'stop' => '0577',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => null,
                    'rules' => 'A',
                    'start' => '1140',
                    'stop' => '1176',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'LF',
                    'rules' => 'A',
                    'start' => '2201',
                    'stop' => '2277',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'LE',
                    'rules' => 'A',
                    'start' => '2201',
                    'stop' => '2277',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'LP',
                    'rules' => 'A',
                    'start' => '2201',
                    'stop' => '2277',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'GC',
                    'rules' => 'A',
                    'start' => '2201',
                    'stop' => '2277',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'FA',
                    'rules' => 'A',
                    'start' => '2201',
                    'stop' => '2277',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EG',
                    'rules' => 'A',
                    'start' => '3260',
                    'stop' => '3277',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EG',
                    'rules' => 'A',
                    'start' => '3370',
                    'stop' => '3377',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'ED',
                    'rules' => 'A',
                    'start' => '3401',
                    'stop' => '3457',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EH',
                    'rules' => 'A',
                    'start' => '3401',
                    'stop' => '3457',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EB',
                    'rules' => 'A',
                    'start' => '3401',
                    'stop' => '3457',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EG',
                    'rules' => 'A',
                    'start' => '4354',
                    'stop' => '4377',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EI',
                    'rules' => 'A',
                    'start' => '4430',
                    'stop' => '4477',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EG',
                    'rules' => 'A',
                    'start' => '5013',
                    'stop' => '5017',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => null,
                    'rules' => 'A',
                    'start' => '5201',
                    'stop' => '5260',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EG',
                    'rules' => 'A',
                    'start' => '5401',
                    'stop' => '5477',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EG',
                    'rules' => 'A',
                    'start' => '6001',
                    'stop' => '6007',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EG',
                    'rules' => 'A',
                    'start' => '6010',
                    'stop' => '6037',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'U',
                    'rules' => 'A',
                    'start' => '6230',
                    'stop' => '6247',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'ES',
                    'rules' => 'A',
                    'start' => '6230',
                    'stop' => '6247',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EN',
                    'rules' => 'A',
                    'start' => '6230',
                    'stop' => '6247',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EF',
                    'rules' => 'A',
                    'start' => '6230',
                    'stop' => '6247',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EK',
                    'rules' => 'A',
                    'start' => '6230',
                    'stop' => '6247',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EH',
                    'rules' => 'A',
                    'start' => '6250',
                    'stop' => '6257',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'LF',
                    'rules' => 'A',
                    'start' => '6301',
                    'stop' => '6377',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EG',
                    'rules' => 'A',
                    'start' => '6460',
                    'stop' => '6477',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EG',
                    'rules' => 'A',
                    'start' => '7014',
                    'stop' => '7027',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'LF',
                    'rules' => 'A',
                    'start' => '7250',
                    'stop' => '7257',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'LEBL',
                    'rules' => 'A',
                    'start' => '7250',
                    'stop' => '7257',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EH',
                    'rules' => 'A',
                    'start' => '7310',
                    'stop' => '7327',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EG',
                    'rules' => 'A',
                    'start' => '7402',
                    'stop' => '7437',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'K',
                    'rules' => 'A',
                    'start' => '7620',
                    'stop' => '7657',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'C',
                    'rules' => 'A',
                    'start' => '7620',
                    'stop' => '7657',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'T',
                    'rules' => 'A',
                    'start' => '7620',
                    'stop' => '7657',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'K',
                    'rules' => 'A',
                    'start' => '7660',
                    'stop' => '7677',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'C',
                    'rules' => 'A',
                    'start' => '7660',
                    'stop' => '7677',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'T',
                    'rules' => 'A',
                    'start' => '7660',
                    'stop' => '7677',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'LF',
                    'rules' => 'A',
                    'start' => '7701',
                    'stop' => '7717',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'LE',
                    'rules' => 'A',
                    'start' => '7701',
                    'stop' => '7717',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EGJA',
                    'rules' => 'A',
                    'start' => '7760',
                    'stop' => '7775',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EGJB',
                    'rules' => 'A',
                    'start' => '7760',
                    'stop' => '7775',
                ],
                [
                    'departure_ident' => 'EG',
                    'arrival_ident' => 'EGJJ',
                    'rules' => 'A',
                    'start' => '7760',
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
        // Nothing to be done here
    }
}
