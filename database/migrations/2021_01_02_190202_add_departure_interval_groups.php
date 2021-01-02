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
