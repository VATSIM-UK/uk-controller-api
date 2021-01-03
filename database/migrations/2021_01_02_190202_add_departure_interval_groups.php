<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDepartureIntervalGroups extends Migration
{
    const GROUPS = [
        'EGBB_SID',
        'EGCC_SANBA_23',
        'EGCC_LISTO_23',
        'EGCC_NORTH_EAST_23',
        'EGCC_EKLAD_KUXEM_23',
        'EGCC_MONTY_23',
        'EGCC_LISTO_05',
        'EGCC_MONTY_05',
        'EGCC_ASMIM_05',
        'EGCC_DESIG_05',
        'EGCC_POL_05',
        'EGCN_ROGAG_02',
        'EGCN_ROGAG_20',
        'EGCN_UPTON_20',
        'EGCN_UPTON_02',
        'EGFF_SID',
        'EGGD_SID',
        'EGGP_SID_EAST',
        'EGGP_SID',
        'EGGW_CPT_26',
        'EGGW_DET_MATCH_26',
        'EGGW_OLNEY_26',
        'EGGW_CPT_08',
        'EGGW_DET_MATCH_08',
        'EGGW_OLNEY_08',
        'EGJB_SID',
        'EGJJ_SID',
        'EGKK_EAST_26',
        'EGKK_BIG_26',
        'EGKK_WEST_26',
        'EGKK_SFD_26',
        'EGKK_RELIEF_26',
        'EGKK_LAM_08',
        'EGKK_EAST_08',
        'EGKK_BIG_08',
        'EGKK_WEST_08',
        'EGKK_SFD_08',
        'EGLC_SID_NORTH_WEST',
        'EGLC_SID_SOUTH_EAST',
        'EGLL_NORTH_27',
        'EGLL_WEST_27L',
        'EGLL_WEST_27R',
        'EGLL_MAXIT_27',
        'EGLL_DET_27',
        'EGLL_NORTH_09',
        'EGLL_CPT_09',
        'EGLL_GASGU_09',
        'EGLL_MODMI_09',
        'EGLL_DET_09L',
        'EGLL_DET_09R',
        'EGLF_SID',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $formattedGroups = [];
        foreach (self::GROUPS as $group) {
            $formattedGroups[] = [
                'key' => $group,
                'created_at' => Carbon::now(),
            ];
        }

        DB::table('sid_departure_interval_groups')->insert($formattedGroups);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('sid_departure_interval_groups')->delete();
    }
}
