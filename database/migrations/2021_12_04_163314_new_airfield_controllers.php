<?php

use App\Services\AirfieldService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewAirfieldControllers extends Migration
{
    const AIRFIELDS = [
        [
            'EGPG_R_TWR',
            '120.6',
        ],
        [
            'EGLD_I_TWR',
            '130.720',
        ],
        [
            'EGSU_I_TWR',
            '122.070',
        ],
        [
            'EGAB_R_TWR',
            '123.200',
        ],
        [
            'EGPI_I_TWR',
            '123.150',
        ],
        [
            'EGHF_I_TWR',
            '118.920',
        ],
        [
            'EGAD_R_TWR',
            '128.300',
        ],
        [
            'EGBK_I_TWR',
            '122.700',
        ],
        [
            'EGKR_TWR',
            '119.600',
        ],
        [
            'EGNO_TWR',
            '130.800',
            'EGNO_P_APP',
            '129.720',
            'EGNO_APP',
            '129.520',
        ],
        [
            'EGBW_I_TWR',
            '124.025',
        ],
        [
            'EGTB_R_TWR',
            '126.550',
        ],
        [
            'EGHG_TWR',
            '125.400',
            'EGHG_A_APP',
            '130.800',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $controllersToInsert = [];
        foreach (self::AIRFIELDS as $controllerData) {
            for ($i = 0; $i < count($controllerData); $i += 2) {
                $controllersToInsert[] = [
                    'callsign' => $controllerData[$i],
                    'frequency' => $controllerData[$i + 1],
                    'requests_departure_releases' => true,
                    'receives_departure_releases' => Str::substr($controllerData[$i], -3) === 'APP',
                    'sends_prenotes' => true,
                    'receives_prenotes' => Str::substr($controllerData[$i], -3) === 'APP',
                    'created_at' => Carbon::now(),
                ];
            }
        }
        DB::table('controller_positions')
            ->upsert($controllersToInsert, ['callsign']);

        foreach (self::AIRFIELDS as $airfield) {
            AirfieldService::createNewTopDownOrder(
                Str::substr($airfield[0], 0, 4),
                array_filter($airfield, function ($key) {
                    return $key % 2 === 0;
                }, ARRAY_FILTER_USE_KEY)
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
