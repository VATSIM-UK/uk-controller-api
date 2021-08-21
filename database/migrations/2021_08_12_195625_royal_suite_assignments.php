<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RoyalSuiteAssignments extends Migration
{
    const STAND_PRIORITIES = [
        '450' => 100,
        '451' => 101, // Should always be lowest priority
        '452' => 99,
        '453' => 98,
        '454' => 97,
        '455' => 96,
        '456' => 95,
        '457' => 94,
        '457R' => 94,
        '457L' => 94,
    ];

    const AIRLINES = [
        'AF1',
        'BAE',
        'BRO',
        'CLF',
        'CRV',
        'EDC',
        'FLJ',
        'GMA',
        'KRF',
        'KRH',
        'LCY',
        'LNX',
        'NJE',
        'NJU',
        'NOH',
        'RRF',
        'RRR',
        'SYG',
        'SXN',
        'VCG',
        'VIP',
        'VJT',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add some missing airlines
        $airlinesToAdd = [
            [
                'icao_code' => 'AF1',
                'name' => 'Air Force 1',
                'callsign' => 'AF1',
                'is_cargo' => false,
                'created_at' => Carbon::now(),
            ],
            [
                'icao_code' => 'FLJ',
                'name' => 'FlairJet Limited',
                'callsign' => 'FLAIRJET',
                'is_cargo' => false,
                'created_at' => Carbon::now(),
            ],
            [
                'icao_code' => 'NJE',
                'name' => 'NetJets Europe',
                'callsign' => 'FRACTION',
                'is_cargo' => false,
                'created_at' => Carbon::now(),
            ],
            [
                'icao_code' => 'NJU',
                'name' => 'NetJets Air Transport UK',
                'callsign' => 'FRACTION',
                'is_cargo' => false,
                'created_at' => Carbon::now(),
            ],
            [
                'icao_code' => 'SXN',
                'name' => 'SaxonAir Charter Limited',
                'callsign' => 'SAXONAIR',
                'is_cargo' => false,
                'created_at' => Carbon::now(),
            ],
            [
                'icao_code' => 'VCG',
                'name' => 'Catreus AOC Limited',
                'callsign' => 'Thunder Cat',
                'is_cargo' => false,
                'created_at' => Carbon::now(),
            ],
        ];
        DB::table('airlines')->insert($airlinesToAdd);

        // Set stand assignment priority
        $heathrow = DB::table('airfield')->where('code', 'EGLL')->first()->id;
        $standIds = [];

        foreach (self::STAND_PRIORITIES as $stand => $priority) {
            $standId = DB::table('stands')->where('airfield_id', $heathrow)->where('identifier', $stand)->first()->id;
            $standIds[] = $standId;

            DB::table('stands')->where('id', $standId)
                ->update(['updated_at' => Carbon::now(), 'assignment_priority' => $priority]);
        }

        // Assign the air+lines
        $allAssignments = [];
        foreach (self::AIRLINES as $airline) {
            $airlineId = DB::table('airlines')->where('icao_code', $airline)->first()->id;

            foreach ($standIds as $standId) {
                $allAssignments[] = [
                    'airline_id' => $airlineId,
                    'stand_id' => $standId,
                    'created_at' => Carbon::now(),
                ];
            }
        }

        DB::table('airline_stand')->insert($allAssignments);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
