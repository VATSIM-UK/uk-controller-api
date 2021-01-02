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
        'EGCC_EKLAD_KUXEM_23' => [
            'EKLAD1R',
            'EKLAD1Y',
            'KUXEM1R',
            'KUXEM1Y',
        ],
        'EGCC_MONTY_23' => [
            'MONTY1R',
            'MONTY1Y',
        ],
        'EGCC_NORTH_EAST_23' => [
            'POL5R',
            'POL1Y',
            'SONEX1R',
            'SONEX1Y',
        ],
        'EGCC_LISTO_05' => [
            'LISTO2Z',
            'LISTO2S',
        ],
        'EGCC_MONTY_05' => [
            'MONTY1Z',
            'MONTY1S',
        ],
        'EGCC_ASMIM_05' => [
            'ASMIM1Z',
            'ASMIM1S',
        ],
        'EGCC_DESIG_05' => [
            'DESIG1Z',
            'DESIG1S',
        ],
        'EGCC_POL_05' => [
            'POL1Z',
            'POL4S',
        ],
        'EGCN_ROGAG_02' => [
            'ROGAG2C',
        ],
        'EGCN_UPTON_02' => [
            'UPTON2C',
        ],
        'EGCN_ROGAG_20' => [
            'ROGAG2A',
        ],
        'EGCN_UPTON_20' => [
            'UPTON2A',
        ],
        'EGFF_SID' => [
            'BCN1A',
            'BCN1B',
            'EXMOR1A',
            'EXMOR1B',
            'ALVIN1B',
        ],
        'EGGD_SID' => [
            'BCN1X',
            'BCN1Z',
            'BADIM1X',
            'WOTAN1Z',
            'EXMOR1X',
            'EXMOR1Z',
        ],
        'EGGP_SID_EAST' => [
            'BARTN1T',
            'BARTN1V',
            'POL4T',
            'POL5V',
        ],
        'EGGP_SID' => [
            'WAL2V',
            'WAL2T',
            'POL4T',
            'POL5V',
            'NANTI2V',
            'NANTI2T',
        ]
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
