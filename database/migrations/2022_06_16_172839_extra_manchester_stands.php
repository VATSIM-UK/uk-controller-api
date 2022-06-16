<?php

use App\Models\Aircraft\WakeCategory;
use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use App\Models\Stand\Stand;
use App\Services\SectorfileService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ExtraManchesterStands extends Migration
{
    const PIER_1_HEAVY_STANDS = [
        [
            '104',
            '532208.78N 0021707.94W',
            '1',
        ],
        [
            '106',
            '532207.32N 0021710.97W',
            '1',
        ],
        [
            '108',
            '532205.87N 0021711.31W',
            '3',
        ],
        [
            '110',
            '532205.70N 0021713.85W',
            '1',
        ],
        [
            '112',
            '532204.02N 0021716.64W',
            '1',
        ],
        [
            '113',
            '532203.09N 0021720.09W',
            '2',
        ],
        [
            '114',
            '532202.85N 0021717.46W',
            '3',
        ],
        [
            '116',
            '532202.36N 0021719.45W',
            '2',
        ],
    ];

    const PIER_1_STAND_PAIRINGS = [
        '106' => [
            '108',
        ],
        '108' => [
            '106',
            '110',
        ],
        '110' => [
            '108',
        ],
        '112' => [
            '114',
        ],
        '114' => [
            '112',
            '116',
        ],
        '116' => [
            '114',
        ],
    ];

    const PIER_1_LOWER_MEDIUM_STANDS = [
        [
            '101',
            '532210.66N 0021710.66W',
            '1',
        ],
        [
            '103',
            '532209.65N 0021712.33W',
            '1',
        ],
        [
            '105',
            '532208.64N 0021714.01W',
            '1',
        ],
        [
            '107',
            '532207.63N 0021715.68W',
            '1',
        ],
        [
            '109',
            '532206.61N 0021717.36W',
            '1',
        ],
        [
            '111',
            '532205.60N 0021719.03W',
            '1',
        ],
    ];

    const REMOTE_STANDS = [
        [
            '905',
            '532212.97N 0021721.19W',
        ],
        [
            '907',
            '532211.96N 0021722.87W',
        ],
        [
            '909',
            '532210.95N 0021724.54W',
        ],
        [
            '911',
            '532209.93N 0021726.22W',
        ],
        [
            '913',
            '532208.92N 0021727.89W',
        ],
        [
            '915',
            '532207.73N 0021729.65W',
        ],
        [
            '917',
            '532206.25N 0021730.65W',
        ],
        [
            '919',
            '532204.91N 0021731.45W',
        ],
        [
            '925',
            '532157.88N 0021726.05W',
        ],
        [
            '927',
            '532156.87N 0021724.36W',
        ],
        [
            '929',
            '532155.87N 0021722.67W',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            $heavy = WakeCategory::where('code', 'H')
                ->whereHas('scheme', function (Builder $scheme) {
                    $scheme->where('key', 'UK');
                })
                ->firstOrFail()
                ->id;

            $lowerMedium = WakeCategory::where('code', 'LM')
                ->whereHas('scheme', function (Builder $scheme) {
                    $scheme->where('key', 'UK');
                })
                ->firstOrFail()
                ->id;

            $manchester = Airfield::where('code', 'EGCC')
                ->firstOrFail()
                ->id;

            $terminal2 = Terminal::where('airfield_id', $manchester)
                ->where('key', 'EGCC_T2')
                ->firstOrFail()
                ->id;

            // Create the heavy stands
            Stand::insert(
                array_map(
                    function (array $stand) use ($manchester, $terminal2, $heavy) {
                        $coordinateSplit = explode(' ', $stand[1]);
                        $coordinate = SectorfileService::coordinateFromNats($coordinateSplit[0], $coordinateSplit[1]);

                        return [
                            'airfield_id' => $manchester,
                            'identifier' => $stand[0],
                            'assignment_priority' => $stand[2],
                            'wake_category_id' => $heavy,
                            'terminal_id' => $terminal2,
                            'latitude' => $coordinate->getLat(),
                            'longitude' => $coordinate->getLng(),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                    },
                    self::PIER_1_HEAVY_STANDS
                )
            );

            // Add some pairings to T2
            foreach (self::PIER_1_STAND_PAIRINGS as $stand => $pairedStands) {
                Stand::where('identifier', $stand)
                    ->airfield('EGCC')
                    ->firstOrFail()
                    ->pairedStands()
                    ->sync(Stand::whereIn('identifier', $pairedStands)->airfield('EGCC')->pluck('id'));
            }

            // Create the LM stands on pier 1
            Stand::insert(
                array_map(
                    function (array $stand) use ($manchester, $terminal2, $lowerMedium) {
                        $coordinateSplit = explode(' ', $stand[1]);
                        $coordinate = SectorfileService::coordinateFromNats($coordinateSplit[0], $coordinateSplit[1]);

                        return [
                            'airfield_id' => $manchester,
                            'identifier' => $stand[0],
                            'assignment_priority' => $stand[2],
                            'wake_category_id' => $lowerMedium,
                            'terminal_id' => $terminal2,
                            'latitude' => $coordinate->getLat(),
                            'longitude' => $coordinate->getLng(),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                    },
                    self::PIER_1_LOWER_MEDIUM_STANDS
                )
            );

            // Create the remote stands
            Stand::insert(
                array_map(
                    function (array $stand) use ($manchester, $lowerMedium) {
                        $coordinateSplit = explode(' ', $stand[1]);
                        $coordinate = SectorfileService::coordinateFromNats($coordinateSplit[0], $coordinateSplit[1]);

                        return [
                            'airfield_id' => $manchester,
                            'identifier' => $stand[0],
                            'wake_category_id' => $lowerMedium,
                            'latitude' => $coordinate->getLat(),
                            'longitude' => $coordinate->getLng(),
                            'assignment_priority' => 100,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];
                    },
                    self::REMOTE_STANDS
                )
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Stand::whereIn(
            'identifier',
            array_merge(
                array_column(self::PIER_1_HEAVY_STANDS, 0),
                array_column(self::PIER_1_LOWER_MEDIUM_STANDS, 0),
                array_column(self::REMOTE_STANDS, 0)
            )
        )
            ->airfield('EGCC')
            ->delete();
    }
}
