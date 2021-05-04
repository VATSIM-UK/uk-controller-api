<?php

use App\Services\SectorfileService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddMissingHeathrowStands extends Migration
{
    const STANDS = [
        '209' => [
            'latitude' => 'N051.28.25.980',
            'longitude' => 'W000.26.59.030',
            'wtc' => 'H',
        ],
        '209L' => [
            'latitude' => 'N051.28.27.080',
            'longitude' => 'W000.26.57.900',
            'wtc' => 'LM',
            'pair' => '209'
        ],
        '209R' => [
            'latitude' => 'N051.28.26.090',
            'longitude' => 'W000.27.00.170',
            'wtc' => 'LM',
            'pair' => '209'
        ],
        '210' => [
            'latitude' => 'N051.28.25.990',
            'longitude' => 'W000.26.54.420',
            'wtc' => 'H',
        ],
        '210L' => [
            'latitude' => 'N051.28.27.100',
            'longitude' => 'W000.26.53.290',
            'wtc' => 'LM',
            'pair' => '210'
        ],
        '210R' => [
            'latitude' => 'N051.28.26.100',
            'longitude' => 'W000.26.55.560',
            'wtc' => 'LM',
            'pair' => '210'
        ],
        '211' => [
            'latitude' => 'N051.28.27.340',
            'longitude' => 'W000.26.51.070',
            'wtc' => 'H',
        ],
        '212' => [
            'latitude' => 'N051.28.24.380',
            'longitude' => 'W000.26.51.170',
            'wtc' => 'H',
        ],
        '212L' => [
            'latitude' => 'N051.28.23.680',
            'longitude' => 'W000.26.49.380',
            'wtc' => 'LM',
            'pair' => '212'
        ],
        '212R' => [
            'latitude' => 'N051.28.25.090',
            'longitude' => 'W000.26.50.990',
            'wtc' => 'LM',
            'pair' => '212'
        ],
        '213' => [
            'latitude' => 'N051.28.21.500',
            'longitude' => 'W000.26.51.140',
            'wtc' => 'H',
        ],
        '213L' => [
            'latitude' => 'N051.28.20.790',
            'longitude' => 'W000.26.49.360',
            'wtc' => 'LM',
            'pair' => '213'
        ],
        '213R' => [
            'latitude' => 'N051.28.22.210',
            'longitude' => 'W000.26.50.960',
            'wtc' => 'LM',
            'pair' => '213'
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $heathrow = DB::table('airfield')->where('code', 'EGLL')->first()->id;
        $terminal = DB::table('terminals')->where('key', 'EGLL_T2B')->first()->id;
        $createdStands = [];
        foreach (self::STANDS as $identifier => $data) {
            $latLong = SectorfileService::coordinateFromSectorfile($data['latitude'], $data['longitude']);
            $createdStands[$identifier] = DB::table('stands')->insertGetId(
                [
                    'identifier' => $identifier,
                    'airfield_id' => $heathrow,
                    'latitude' => $latLong->getLat(),
                    'longitude' => $latLong->getLng(),
                    'wake_category_id' => DB::table('wake_categories')->where('code', $data['wtc'])->first()->id,
                    'terminal_id' => $terminal,
                    'assignment_priority' => 100,
                    'created_at' => Carbon::now(),
                ]
            );

            if (isset($data['pair'])) {
                DB::table('stand_pairs')->insert(
                    [
                        [
                            'stand_id' => $createdStands[$identifier],
                            'paired_stand_id' => $createdStands[$data['pair']],
                        ],
                        [
                            'stand_id' => $createdStands[$data['pair']],
                            'paired_stand_id' => $createdStands[$identifier],
                        ],
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
        //
    }
}
