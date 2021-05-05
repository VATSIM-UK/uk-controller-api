<?php

use App\Services\SectorfileService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\DependencyService;
use App\Services\StandService;

class AddMissingStanstedStands extends Migration
{
    const STANDS_TO_DEPRIORITISE = [
        '1',
        '9',
        '11',
        '12',
        '13',
        '15',
        '22',
        '23',
        '24',
        '32',
        '33',
        '34',
        '42',
        '43',
        '44',
        '50',
        '51',
        '52',
        '53',
        '61',
        '62',
        '63',
        '64',
    ];

    const NEW_MAIN_STANDS_TO_ADD = [
        '1L N051.53.10.280 E000.15.10.990',
        '1R N051.53.11.000 E000.15.09.250',
        '9L N051.53.17.390 E000.14.59.460',
        '9R N051.53.18.070 E000.14.57.350',
        '11L N051.53.17.810 E000.15.16.360',
        '11R N051.53.16.910 E000.15.17.950',
        '12L N051.53.19.300 E000.15.12.710',
        '12R N051.53.18.730 E000.15.14.790',
        '13L N051.53.21.120 E000.15.09.540',
        '13R N051.53.20.630 E000.15.11.740',
        '15L N051.53.24.180 E000.15.05.110',
        '15R N051.53.23.240 E000.15.06.740',
        '22L N051.53.21.810 E000.15.17.630',
        '22R N051.53.22.730 E000.15.16.070',
        '23L N051.53.23.910 E000.15.14.700',
        '23R N051.53.24.560 E000.15.12.740',
        '24L N051.53.25.610 E000.15.11.350',
        '24R N051.53.26.360 E000.15.10.160',
        '32R N051.53.26.500 E000.15.24.460',
        '32L N051.53.27.290 E000.15.22.720',
        '42L N051.53.28.950 E000.15.27.980',
        '42R N051.53.29.510 E000.15.26.050',
        '43L N051.53.30.590 E000.15.24.770',
        '43R N051.53.31.310 E000.15.22.910',
        '44L N051.53.32.340 E000.15.21.500',
        '44R N051.53.32.960 E000.15.20.380',
        '50L N051.53.32.020 E000.15.36.670',
        '50R N051.53.31.190 E000.15.38.390',
        '51L N051.53.33.830 E000.15.33.520',
        '51R N051.53.32.930 E000.15.35.100',
        '52L N051.53.35.680 E000.15.30.300',
        '52R N051.53.34.890 E000.15.32.060',
        '53L N051.53.37.530 E000.15.27.070',
        '53R N051.53.36.720 E000.15.28.840',
        '61L N051.53.33.370 E000.15.41.820',
        '61R N051.53.34.020 E000.15.39.830',
        '62L N051.53.35.240 E000.15.38.560',
        '62R N051.53.35.890 E000.15.36.580',
        '63L N051.53.37.090 E000.15.35.340',
        '63R N051.53.37.730 E000.15.33.340',
        '64L N051.53.38.600 E000.15.31.610',
        '64R N051.53.39.810 E000.15.30.610',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get data for the transformations
        $stansted = DB::table('airfield')->where('code', 'EGSS')->first()->id;
        $mappedStandsToDeprioritise = DB::table('stands')
            ->whereIn('identifier', self::STANDS_TO_DEPRIORITISE)
            ->where('airfield_id', $stansted)
            ->get()
            ->mapWithKeys(
                function ($stand) {
                    return [
                        $stand->identifier => [
                            'id' => $stand->id,
                            'assignment_priority' => $stand->assignment_priority,
                            'type_id' => $stand->type_id,
                        ]
                    ];
                }
            )
            ->toArray();
        $lowerMedium = DB::table('wake_categories')->where('code', 'LM')->first()->id;

        foreach (self::NEW_MAIN_STANDS_TO_ADD as $stand) {
            $standParts = explode(' ', $stand);
            $coordinate = SectorfileService::coordinateFromSectorfile($standParts[1], $standParts[2]);
            $relatedStand = $mappedStandsToDeprioritise[Str::substr($standParts[0], 0, -1)];

            // Add the stand
            $standId = DB::table('stands')->insertGetId(
                [
                    'identifier' => $standParts[0],
                    'airfield_id' => $stansted,
                    'latitude' => $coordinate->getLat(),
                    'longitude' => $coordinate->getLng(),
                    'assignment_priority' => $relatedStand['assignment_priority'],
                    'wake_category_id' => $lowerMedium,
                    'type_id' => $relatedStand['type_id']
                ]
            );

            // Add the pairing to prevent the "middle" stand being used at the same as the L/R
            DB::table('stand_pairs')->insert(
                [
                    [
                        'stand_id' => $standId,
                        'paired_stand_id' => $relatedStand['id'],
                    ],
                    [
                        'stand_id' => $relatedStand['id'],
                        'paired_stand_id' => $standId,
                    ],
                ]
            );

            // Add airline mappings
            $airlinePairings = DB::table('airline_stand')
                ->where('stand_id', $relatedStand['id'])
                ->get()
                ->map(function ($pairing) use ($standId) {
                    return [
                        'airline_id' => $pairing->airline_id,
                        'stand_id' => $standId,
                        'destination' => $pairing->destination,
                        'not_before' => $pairing->not_before,
                        'created_at' => Carbon::now(),
                    ];
                })
                ->toArray();
            
            if (!empty($airlinePairings)) {
                DB::table('airline_stand')->insert($airlinePairings);
            }
        }


        // Deprioritise the non L/R stands
        DB::table('stands')
            ->whereIn('id', array_column($mappedStandsToDeprioritise, 'id'))
            ->update(['assignment_priority' => DB::raw('`assignment_priority` + 1')]);
        
        DependencyService::touchDependencyByKey(StandService::STAND_DEPENDENCY_KEY);
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
