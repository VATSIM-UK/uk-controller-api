<?php

use App\Models\Airfield\Airfield;
use App\Models\Controller\Handoff;
use App\Models\Runway\Runway;
use App\Services\HandoffService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddMissingSidData extends Migration
{
    const DATA = [
        'EGBB' => [
            [
                'LUXTO15',
                '15',
                '6000',
                'EGBB_SID',
            ],
            [
                'MOSUN15',
                '15',
                '6000',
                'EGBB_SID',
            ],
        ],
        'EGKB' => [
            [
                'BPK2',
                '03',
                '2400',
                'EGKB_DEPARTURE',
            ],
            [
                'BPK2',
                '21',
                '2400',
                'EGKB_DEPARTURE',
            ],
            [
                'DAGGA2',
                '03',
                '2400',
                'EGKB_DEPARTURE',
            ],
            [
                'DAGGA2',
                '21',
                '2400',
                'EGKB_DEPARTURE',
            ],
            [
                'DVR2',
                '03',
                '2400',
                'EGKB_DEPARTURE',
            ],
            [
                'DVR2',
                '21',
                '2400',
                'EGKB_DEPARTURE',
            ],
            [
                'LYD2',
                '03',
                '2400',
                'EGKB_DEPARTURE',
            ],
            [
                'LYD2',
                '21',
                '2400',
                'EGKB_DEPARTURE',
            ],
            [
                'CPT2',
                '03',
                '2400',
                'EGKB_DEPARTURE',
            ],
            [
                'CPT2',
                '21',
                '2400',
                'EGKB_DEPARTURE',
            ],
        ],
        'EGKK' => [
            [
                'FRANE1W',
                '08L',
                '4000',
                'EGKK_SID_EAST'
            ],
            [
                'DVR2W',
                '08L',
                '6000',
                'EGKK_SID_EAST'
            ],
            [
                'NOVMA1M',
                '26L',
                '4000',
                'EGKK_SID_WEST'
            ],
            [
                'NOVMA1M',
                '26R',
                '4000',
                'EGKK_SID_WEST'
            ],
            [
                'CLN5W',
                '08L',
                '4000',
                'EGKK_SID_EAST'
            ],
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add a missing handoff order for EGKB
        HandoffService::createNewHandoffOrder(
            'EGKB_DEPARTURE',
            'Biggin Hill Standard Route Departures',
            [
                'THAMES_APP',
                'LTC_SE_CTR',
                'LTC_S_CTR',
                'LTC_CTR',
                'LON_D_CTR',
                'LON_S_CTR',
                'LON_SC_CTR',
                'LON_CTR',
            ]
        );

        $airfields = Airfield::whereIn('code', array_keys(self::DATA))
            ->get()
            ->mapWithKeys(fn(Airfield $airfield) => [$airfield->code => $airfield->id])
            ->toArray();

        $runways = Runway::whereIn('airfield_id', array_values($airfields))
            ->get()
            ->mapWithKeys(
                fn(Runway $runway) => [$this->getRunwayKey($runway->airfield_id, $runway->identifier) => $runway->id]
            )
            ->toArray();

        $handoffs = Handoff::all()->mapWithKeys(fn(Handoff $handoff) => [$handoff->key => $handoff->id])->toArray();

        $insertData = [];
        foreach (self::DATA as $airfield => $sids) {
            $insertData = array_merge(
                $insertData,
                array_map(fn(array $sidData) => [
                    'airfield_id' => $airfields[$airfield],
                    'runway_id' => $runways[$this->getRunwayKey($airfields[$airfield], $sidData[1])],
                    'identifier' => $sidData[0],
                    'initial_altitude' => $sidData[2],
                    'handoff_id' => $handoffs[$sidData[3]],
                    'created_at' => Carbon::now(),
                ], $sids)
            );
        }

        DB::table('sid')
            ->insert($insertData);
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

    private function getRunwayKey(int $airfield, string $runway): string
    {
        return sprintf('%s:%s', $airfield, $runway);
    }
}
