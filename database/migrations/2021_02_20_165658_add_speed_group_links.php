<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddSpeedGroupLinks extends Migration
{
    /*
     * Lead group => follow group => penalty
     */
    const PENALTY_GROUPS = [
        'EGKK_GROUP_1' => [
            'EGKK_GROUP_6' => 5,
            'EGKK_GROUP_5' => 4,
            'EGKK_GROUP_4' => 3,
            'EGKK_GROUP_3' => 2,
            'EGKK_GROUP_2' => 1,
        ],
        'EGKK_GROUP_2' => [
            'EGKK_GROUP_6' => 4,
            'EGKK_GROUP_5' => 3,
            'EGKK_GROUP_4' => 2,
            'EGKK_GROUP_3' => 1,
        ],
        'EGKK_GROUP_3' => [
            'EGKK_GROUP_6' => 3,
            'EGKK_GROUP_5' => 2,
            'EGKK_GROUP_4' => 1,
            'EGKK_GROUP_1' => -1,
        ],
        'EGKK_GROUP_4' => [
            'EGKK_GROUP_6' => 2,
            'EGKK_GROUP_5' => 1,
            'EGKK_GROUP_2' => -1,
            'EGKK_GROUP_1' => -1,
        ],
        'EGKK_GROUP_5' => [
            'EGKK_GROUP_6' => 1,
            'EGKK_GROUP_3' => -1,
            'EGKK_GROUP_2' => -1,
            'EGKK_GROUP_1' => -1,
        ],
        'EGKK_GROUP_6' => [
            'EGKK_GROUP_4' => -1,
            'EGKK_GROUP_3' => -1,
            'EGKK_GROUP_2' => -1,
            'EGKK_GROUP_1' => -1,
        ],
        'EGLL_GROUP_0' => [
            'EGLL_GROUP_4' => 4,
            'EGLL_GROUP_3' => 3,
            'EGLL_GROUP_2' => 2,
            'EGLL_GROUP_1' => 1,
        ],
        'EGLL_GROUP_1' => [
            'EGLL_GROUP_4' => 3,
            'EGLL_GROUP_3' => 2,
            'EGLL_GROUP_2' => 1,
        ],
        'EGLL_GROUP_2' => [
            'EGLL_GROUP_4' => 2,
            'EGLL_GROUP_3' => 1,
        ],
        'EGLL_GROUP_3' => [
            'EGLL_GROUP_4' => 1,
        ],
        'EGLC_GROUP_3' => [
            'EGLC_GROUP_2' => 1,
            'EGLC_GROUP_1' => 2,
            'EGLC_GROUP_0' => 2,
        ],
        'EGLC_GROUP_2' => [
            'EGLC_GROUP_1' => 2,
            'EGLC_GROUP_0' => 2,
        ],
        'EGLC_GROUP_1' => [
            'EGLC_GROUP_0' => 1,
            'EGLC_GROUP_3' => -1,
        ],
        'EGLC_GROUP_0' => [
            'EGLC_GROUP_3' => -1,
            'EGLC_GROUP_2' => -1,
        ],
        'EGSS_GROUP_0' => [
            'EGSS_GROUP_1' => 1,
            'EGSS_GROUP_2' => 2,
            'EGSS_GROUP_3' => 3,
            'EGSS_GROUP_4' => 4,
        ],
        'EGSS_GROUP_1' => [
            'EGSS_GROUP_2' => 1,
            'EGSS_GROUP_3' => 2,
            'EGSS_GROUP_4' => 3,
        ],
        'EGSS_GROUP_2' => [
            'EGSS_GROUP_0' => -1,
            'EGSS_GROUP_3' => 1,
            'EGSS_GROUP_4' => 2,
        ],
        'EGSS_GROUP_3' => [
            'EGSS_GROUP_0' => -2,
            'EGSS_GROUP_1' => -1,
            'EGSS_GROUP_4' => 1,
        ],
        'EGSS_GROUP_4' => [
            'EGSS_GROUP_0' => -3,
            'EGSS_GROUP_1' => -2,
            'EGSS_GROUP_2' => -1,
        ],
        'EGGW_GROUP_0' => [
            'EGGW_GROUP_1' => 1,
            'EGGW_GROUP_2' => 2,
            'EGGW_GROUP_3' => 3,
            'EGGW_GROUP_4' => 4,
        ],
        'EGGW_GROUP_1' => [
            'EGGW_GROUP_2' => 1,
            'EGGW_GROUP_3' => 2,
            'EGGW_GROUP_4' => 3,
        ],
        'EGGW_GROUP_2' => [
            'EGGW_GROUP_3' => 1,
            'EGGW_GROUP_4' => 2,
        ],
        'EGGW_GROUP_3' => [
            'EGGW_GROUP_4' => 1,
        ],
        'EGCC_GROUP_0' => [
            'EGCC_GROUP_0' => 1,
            'EGCC_GROUP_1' => 2,
            'EGCC_GROUP_2' => 3,
            'EGCC_GROUP_3' => 4,
            'EGCC_GROUP_4' => 5,
        ],
        'EGCC_GROUP_1' => [
            'EGCC_GROUP_2' => 1,
            'EGCC_GROUP_3' => 2,
            'EGCC_GROUP_4' => 3,
        ],
        'EGCC_GROUP_2' => [
            'EGCC_GROUP_3' => 2,
            'EGCC_GROUP_4' => 3,
        ],
        'EGCC_GROUP_3' => [
            'EGCC_GROUP_4' => 3,
        ],
        'EGGP_GROUP_0' => [
            'EGGP_GROUP_0' => 1,
            'EGGP_GROUP_1' => 2,
            'EGGP_GROUP_2' => 3,
            'EGGP_GROUP_3' => 4,
            'EGGP_GROUP_4' => 5,
        ],
        'EGGP_GROUP_1' => [
            'EGGP_GROUP_2' => 1,
            'EGGP_GROUP_3' => 2,
            'EGGP_GROUP_4' => 3,
        ],
        'EGGP_GROUP_2' => [
            'EGGP_GROUP_3' => 1,
            'EGGP_GROUP_4' => 2,
        ],
        'EGGP_GROUP_3' => [
            'EGGP_GROUP_4' => 1,
        ],
        'EGGD_GROUP_1' => [
            'EGGD_GROUP_1' => 2,
            'EGGD_GROUP_2' => 3,
            'EGGD_GROUP_3' => 4,
            'EGGD_GROUP_4' => 5,
        ],
        'EGGD_GROUP_2' => [
            'EGGD_GROUP_3' => 1,
            'EGGD_GROUP_4' => 2,
        ],
        'EGGD_GROUP_3' => [
            'EGGD_GROUP_4' => 1,
        ],
        'EGFF_GROUP_1' => [
            'EGFF_GROUP_2' => 1,
            'EGFF_GROUP_3' => 2,
            'EGFF_GROUP_4' => 2,
            'EGFF_GROUP_5' => 2,
            'EGFF_GROUP_6' => 2,
        ],
        'EGFF_GROUP_2' => [
            'EGFF_GROUP_3' => 1,
            'EGFF_GROUP_4' => 2,
            'EGFF_GROUP_5' => 2,
            'EGFF_GROUP_6' => 2,
        ],
        'EGFF_GROUP_3' => [
            'EGFF_GROUP_1' => -1,
            'EGFF_GROUP_4' => 1,
            'EGFF_GROUP_5' => 2,
            'EGFF_GROUP_6' => 2,
        ],
        'EGFF_GROUP_4' => [
            'EGFF_GROUP_1' => -1,
            'EGFF_GROUP_2' => -1,
            'EGFF_GROUP_5' => 1,
            'EGFF_GROUP_6' => 2,
        ],
        'EGFF_GROUP_5' => [
            'EGFF_GROUP_1' => -1,
            'EGFF_GROUP_2' => -1,
            'EGFF_GROUP_3' => -1,
            'EGFF_GROUP_6' => 1,
        ],
        'EGPH_GROUP_1' => [
            'EGPH_GROUP_2' => 1,
            'EGPH_GROUP_3' => 2,
            'EGPH_GROUP_4' => 3,
            'EGPH_GROUP_5' => 4,
            'EGPH_GROUP_6' => 5,
        ],
        'EGPH_GROUP_2' => [
            'EGPH_GROUP_3' => 1,
            'EGPH_GROUP_4' => 2,
            'EGPH_GROUP_5' => 3,
            'EGPH_GROUP_6' => 4,
        ],
        'EGPH_GROUP_3' => [
            'EGPH_GROUP_1' => -1,
            'EGPH_GROUP_4' => 1,
            'EGPH_GROUP_5' => 2,
            'EGPH_GROUP_6' => 3,
        ],
        'EGPH_GROUP_4' => [
            'EGPH_GROUP_1' => -1,
            'EGPH_GROUP_2' => -1,
            'EGPH_GROUP_5' => 2,
            'EGPH_GROUP_6' => 3,
        ],
        'EGPH_GROUP_5' => [
            'EGPH_GROUP_1' => -1,
            'EGPH_GROUP_2' => -1,
            'EGPH_GROUP_3' => -1,
            'EGPH_GROUP_6' => 3,
        ],
        'EGPH_GROUP_6' => [
            'EGPH_GROUP_1' => -1,
            'EGPH_GROUP_2' => -1,
            'EGPH_GROUP_3' => -1,
            'EGPH_GROUP_4' => -1,
        ],
        'EGPF_GROUP_0' => [
            'EGPF_GROUP_0' => 1,
            'EGPF_GROUP_1' => 2,
            'EGPF_GROUP_2' => 3,
            'EGPF_GROUP_3' => 4,
            'EGPF_GROUP_4' => 5,
        ],
        'EGPF_GROUP_1' => [
            'EGPF_GROUP_2' => 1,
            'EGPF_GROUP_3' => 2,
            'EGPF_GROUP_4' => 3,
        ],
        'EGPF_GROUP_2' => [
            'EGPF_GROUP_3' => 1,
            'EGPF_GROUP_4' => 2,
        ],
        'EGPF_GROUP_3' => [
            'EGPF_GROUP_4' => 1,
        ],
    ];

    /**
     * Lead group => follow group => set interval to
     */
    const SET_INTERVAL_GROUPS = [
        'EGGW_GROUP_2' => [
            'EGGW_GROUP_0' => 1,
        ],
        'EGGW_GROUP_3' => [
            'EGGW_GROUP_0' => 1,
            'EGGW_GROUP_1' => 1,
        ],
        'EGGW_GROUP_4' => [
            'EGGW_GROUP_0' => 1,
            'EGGW_GROUP_1' => 1,
            'EGGW_GROUP_2' => 1,
        ],
        'EGCC_GROUP_2' => [
            'EGCC_GROUP_0' => 1,
        ],
        'EGCC_GROUP_3' => [
            'EGCC_GROUP_0' => 1,
            'EGCC_GROUP_1' => 1,
        ],
        'EGCC_GROUP_4' => [
            'EGCC_GROUP_0' => 1,
            'EGCC_GROUP_1' => 1,
            'EGCC_GROUP_2' => 1,
        ],
        'EGGP_GROUP_2' => [
            'EGCC_GROUP_0' => 1,
        ],
        'EGGP_GROUP_3' => [
            'EGCC_GROUP_0' => 1,
            'EGCC_GROUP_1' => 1,
        ],
        'EGGP_GROUP_4' => [
            'EGCC_GROUP_0' => 1,
            'EGCC_GROUP_1' => 1,
            'EGCC_GROUP_2' => 1,
        ],
        'EGGD_GROUP_4' => [
            'EGGD_GROUP_2' => 1,
            'EGGD_GROUP_1' => 1,
        ],
        'EGGD_GROUP_3' => [
            'EGGD_GROUP_1' => 1,
        ],
        'EGPF_GROUP_2' => [
            'EGPF_GROUP_0' => 1,
        ],
        'EGPF_GROUP_3' => [
            'EGPF_GROUP_0' => 1,
            'EGPF_GROUP_1' => 1,
        ],
        'EGPF_GROUP_4' => [
            'EGPF_GROUP_0' => 1,
            'EGPF_GROUP_1' => 1,
            'EGPF_GROUP_2' => 1,
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $groups = DB::table('speed_groups')->get()->mapWithKeys(function ($group) {
            return [$group->key => $group->id];
        });

        $mergedLinks = [];
        foreach (self::PENALTY_GROUPS as $leadGroup => $followingGroups) {
            foreach ($followingGroups as $followingGroup => $penalty) {
                $mergedLinks[] = [
                    'lead_speed_group_id' => $groups[$leadGroup],
                    'follow_speed_group_id' => $groups[$followingGroup],
                    'penalty' => $penalty,
                    'created_at' => Carbon::now(),
                ];
            }
        }

        foreach (self::SET_INTERVAL_GROUPS as $leadGroup => $followingGroups) {
            foreach ($followingGroups as $followingGroup => $interval) {
                $mergedLinks[] = [
                    'lead_speed_group_id' => $groups[$leadGroup],
                    'follow_speed_group_id' => $groups[$followingGroup],
                    'set_interval_to' => $interval,
                    'created_at' => Carbon::now(),
                ];
            }
        }

        DB::table('speed_group_speed_group')->insert($mergedLinks);
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
