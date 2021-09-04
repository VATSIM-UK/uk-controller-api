<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class EastMidlandsStandRefresh extends Migration
{
    const MAPPINGS = [
        'ABR' => ['EGNX_E'],
        'AEA' => ['EGNX_C'],
        'BAW' => ['EGNX_W'],
        'BEE' => ['EGNX_C', 'EGNX_CW'],
        'BMR' => ['EGNX_C', 'EGNX_CW'],
        'EXS' => ['EGNX_C', 'EGNX_CW'],
        'TAY' => ['EGNX_E'],
        'STK' => ['EGNX_C'],
        'ETD' => ['EGNX_W'],
        'FDX' => ['EGNX_E'],
        'RVL' => ['EGNX_RVL'],
        'EGL' => ['EGNX_MAINT'],
        'DON' => ['EGNX_MAINT'],
        'HLE' => ['EGNX_MAINT'],
    ];

    const TERMINALS_STANDS = [
        'EGNX_RVL' => [
            [
                'identifier' => 'RVL1',
                'latitude' => 52.82673300788122,
                'longitude' => -1.3403500516892841,
            ],
            [
                'identifier' => 'RVL2',
                'latitude' => 52.82706280681487,
                'longitude' => 1.3384554654888716,
            ],
            [
                'identifier' => 'RVL3',
                'latitude' => 52.8274280357705,
                'longitude' => -1.3403500516892841,
            ],
        ],
        'EGNX_MAINT' => [
            [
                'identifier' => 'MAINT1',
                'latitude' => 52.82675690052209,
                'longitude' => -1.3392578254535752,
            ],
            [
                'identifier' => 'MAINT2',
                'latitude' => 52.8256899464974,
                'longitude' => -1.3381359493504432,
            ],
            [
                'identifier' => 'MAINT3',
                'latitude' => 52.82585485023592,
                'longitude' => -1.3380254318220857,
            ]
        ]
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add some missing (fictitious) stands and terminals for various parking areas
        $eastMids = DB::table('airfield')->where('code', 'EGNX')->first()->id;
        $small = DB::table('wake_categories')->where('code', 'S')->first()->id;

        foreach (self::TERMINALS_STANDS as $terminal => $stands) {
            $terminalId = DB::table('terminals')->insertGetId(
                [
                    'airfield_id' => $eastMids,
                    'key' => $terminal,
                    'description' => $terminal,
                    'created_at' => Carbon::now(),
                ]
            );

            DB::table('stands')->insert(
                array_map(
                    function ($stand) use ($eastMids, $terminalId, $small) {
                        return [
                            'identifier' => $stand['identifier'],
                            'latitude' => $stand['latitude'],
                            'longitude' => $stand['longitude'],
                            'airfield_id' => $eastMids,
                            'terminal_id' => $terminalId,
                            'assignment_priority' => 200,
                            'wake_category_id' => $small,
                            'created_at' => Carbon::now(),
                        ];
                    },
                    $stands
                )
            );
        }

        // Add the affiliations
        $terminalMappings = DB::table('terminals')->whereIn(
            'key',
            ['EGNX_C', 'EGNX_CW', 'EGNX_W', 'EGNX_E', 'EGNX_RVL', 'EGNX_MAINT']
        )->get()->mapWithKeys(function ($terminal) {
            return [$terminal->key => $terminal->id];
        })->toArray();

        $airlines = DB::table('airlines')->whereIn('icao_code', array_keys(self::MAPPINGS))->get()->mapWithKeys(
            function ($airline) {
                return [$airline->icao_code => $airline->id];
            }
        )->toArray();

        foreach (self::MAPPINGS as $airline => $terminals) {
            DB::table('airline_terminal')
                ->whereIn('terminal_id', array_values($terminalMappings))
                ->where('airline_id', $airlines[$airline])
                ->delete();

            DB::table('airline_terminal')->insert(
                array_map(
                    function ($terminal) use ($terminalMappings, $airlines, $airline) {
                        return [
                            'terminal_id' => $terminalMappings[$terminal],
                            'airline_id' => $airlines[$airline],
                            'created_at' => Carbon::now(),
                        ];
                    },
                    $terminals
                )
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
        //
    }
}
