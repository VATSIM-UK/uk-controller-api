<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDepartureIntervalGroupMembers extends Migration
{
    const GROUPS = [
        'EGBB_SID' => [
            'ADMEX1D',
            'ADMEX1M',
            'COWLY2Y',
            'CPT2Y',
            'DTY2Y',
            'DTY4F',
            'LUVUM1L',
            'LUVUM1M',
            'TNT1K',
            'TNT4G',
            'TNT4D',
            'TNT6E',
            'UMLUX1M',
            'UNGAP1D',
            'UNGAP1M',
            'WCO2Y',
            'WCO5D',
            'WHI1L',
            'WHI4D',
        ],
        'EGCC_SANBA_23' => [
            'SANBA1R',
            'SANBA1Y',
        ],
        'EGCC_LISTO_23' => [
            'LISTO2R',
            'LISTO2Y',
        ],
        'EGCC_WEST_23' => [
            'MONTY1R',
            'MONTY1Y',
            'EKLAD1R',
            'EKLAD1Y',
            'KUXEM1R',
            'KUXEM1Y',
        ],
        'EGCC_NORTH_EAST_23' => [
            'POL5R',
            'POL1Y',
            'SONEX1R',
            'SONEX1Y',
        ],
        'EGCC_SOUTH_05' => [
            'LISTO2Z',
            'LISTO2S',
        ],
        'EGCC_WEST_05' => [
            'MONTY1Z',
            'MONTY1S',
            'AZMIM1Z',
            'AZMIM1S',
        ],
        'EGCC_NORTH_EAST_05' => [
            'POL1Z',
            'POL4S',
            'DESIG1Z',
            'DESIG1S',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::GROUPS as $group => $sids) {
            $groupId = DB::table('sid_departure_interval_groups')
                ->where('key', $group)
                ->first()
                ->id;

            DB::table('sid')->whereIn('identifier', $sids)
                ->update(['sid_departure_interval_group_id' => $groupId, 'updated_at' => Carbon::now()]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('sid')->update(['sid_departure_interval_group_id' => null, 'updated_at' => Carbon::now()]);
    }
}
