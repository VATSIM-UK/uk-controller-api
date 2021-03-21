<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddSpeedGroupLinks extends Migration
{
    /*
     * Lead group => follow group => penalty
     */
    const PENALTY_GROUPS = [
        'EGKK_GROUP_1' => [
            'EGKK_GROUP_6' => 300,
            'EGKK_GROUP_5' => 240,
            'EGKK_GROUP_4' => 180,
            'EGKK_GROUP_3' => 120,
            'EGKK_GROUP_2' => 60,
        ],
        'EGKK_GROUP_2' => [
            'EGKK_GROUP_6' => 240,
            'EGKK_GROUP_5' => 180,
            'EGKK_GROUP_4' => 120,
            'EGKK_GROUP_3' => 60,
        ],
        'EGKK_GROUP_3' => [
            'EGKK_GROUP_6' => 180,
            'EGKK_GROUP_5' => 120,
            'EGKK_GROUP_4' => 60,
            'EGKK_GROUP_1' => -60,
        ],
        'EGKK_GROUP_4' => [
            'EGKK_GROUP_6' => 120,
            'EGKK_GROUP_5' => 60,
            'EGKK_GROUP_2' => -60,
            'EGKK_GROUP_1' => -60,
        ],
        'EGKK_GROUP_5' => [
            'EGKK_GROUP_6' => 60,
            'EGKK_GROUP_3' => -60,
            'EGKK_GROUP_2' => -60,
            'EGKK_GROUP_1' => -60,
        ],
        'EGKK_GROUP_6' => [
            'EGKK_GROUP_4' => -60,
            'EGKK_GROUP_3' => -60,
            'EGKK_GROUP_2' => -60,
            'EGKK_GROUP_1' => -60,
        ],
        'EGLL_GROUP_0' => [
            'EGLL_GROUP_4' => 240,
            'EGLL_GROUP_3' => 180,
            'EGLL_GROUP_2' => 120,
            'EGLL_GROUP_1' => 60,
        ],
        'EGLL_GROUP_1' => [
            'EGLL_GROUP_4' => 180,
            'EGLL_GROUP_3' => 120,
            'EGLL_GROUP_2' => 60,
        ],
        'EGLL_GROUP_2' => [
            'EGLL_GROUP_4' => 120,
            'EGLL_GROUP_3' => 60,
        ],
        'EGLL_GROUP_3' => [
            'EGLL_GROUP_4' => 60,
        ],
        'EGLC_GROUP_3' => [
            'EGLC_GROUP_2' => 60,
            'EGLC_GROUP_1' => 120,
            'EGLC_GROUP_0' => 120,
        ],
        'EGLC_GROUP_2' => [
            'EGLC_GROUP_1' => 120,
            'EGLC_GROUP_0' => 120,
        ],
        'EGLC_GROUP_1' => [
            'EGLC_GROUP_0' => 60,
            'EGLC_GROUP_3' => -60,
        ],
        'EGLC_GROUP_0' => [
            'EGLC_GROUP_3' => -60,
            'EGLC_GROUP_2' => -60,
        ],
        'EGSS_GROUP_0' => [
            'EGSS_GROUP_1' => 60,
            'EGSS_GROUP_2' => 120,
            'EGSS_GROUP_3' => 180,
            'EGSS_GROUP_4' => 240,
        ],
        'EGSS_GROUP_1' => [
            'EGSS_GROUP_2' => 60,
            'EGSS_GROUP_3' => 120,
            'EGSS_GROUP_4' => 180,
        ],
        'EGSS_GROUP_2' => [
            'EGSS_GROUP_0' => -60,
            'EGSS_GROUP_3' => 60,
            'EGSS_GROUP_4' => 120,
        ],
        'EGSS_GROUP_3' => [
            'EGSS_GROUP_0' => -120,
            'EGSS_GROUP_1' => -60,
            'EGSS_GROUP_4' => 60,
        ],
        'EGSS_GROUP_4' => [
            'EGSS_GROUP_0' => -180,
            'EGSS_GROUP_1' => -120,
            'EGSS_GROUP_2' => -60,
        ],
        'EGGW_GROUP_0' => [
            'EGGW_GROUP_1' => 60,
            'EGGW_GROUP_2' => 120,
            'EGGW_GROUP_3' => 180,
            'EGGW_GROUP_4' => 240,
        ],
        'EGGW_GROUP_1' => [
            'EGGW_GROUP_2' => 60,
            'EGGW_GROUP_3' => 120,
            'EGGW_GROUP_4' => 180,
        ],
        'EGGW_GROUP_2' => [
            'EGGW_GROUP_3' => 60,
            'EGGW_GROUP_4' => 120,
        ],
        'EGGW_GROUP_3' => [
            'EGGW_GROUP_4' => 60,
        ],
        'EGCC_GROUP_0' => [
            'EGCC_GROUP_0' => 60,
            'EGCC_GROUP_1' => 120,
            'EGCC_GROUP_2' => 180,
            'EGCC_GROUP_3' => 240,
            'EGCC_GROUP_4' => 300,
        ],
        'EGCC_GROUP_1' => [
            'EGCC_GROUP_2' => 60,
            'EGCC_GROUP_3' => 120,
            'EGCC_GROUP_4' => 180,
        ],
        'EGCC_GROUP_2' => [
            'EGCC_GROUP_3' => 120,
            'EGCC_GROUP_4' => 180,
        ],
        'EGCC_GROUP_3' => [
            'EGCC_GROUP_4' => 180,
        ],
        'EGGP_GROUP_0' => [
            'EGGP_GROUP_0' => 60,
            'EGGP_GROUP_1' => 120,
            'EGGP_GROUP_2' => 180,
            'EGGP_GROUP_3' => 240,
            'EGGP_GROUP_4' => 300,
        ],
        'EGGP_GROUP_1' => [
            'EGGP_GROUP_2' => 60,
            'EGGP_GROUP_3' => 120,
            'EGGP_GROUP_4' => 180,
        ],
        'EGGP_GROUP_2' => [
            'EGGP_GROUP_3' => 60,
            'EGGP_GROUP_4' => 120,
        ],
        'EGGP_GROUP_3' => [
            'EGGP_GROUP_4' => 60,
        ],
        'EGGD_GROUP_1' => [
            'EGGD_GROUP_1' => 120,
            'EGGD_GROUP_2' => 180,
            'EGGD_GROUP_3' => 240,
            'EGGD_GROUP_4' => 300,
        ],
        'EGGD_GROUP_2' => [
            'EGGD_GROUP_3' => 60,
            'EGGD_GROUP_4' => 120,
        ],
        'EGGD_GROUP_3' => [
            'EGGD_GROUP_4' => 60,
        ],
        'EGFF_GROUP_1' => [
            'EGFF_GROUP_2' => 60,
            'EGFF_GROUP_3' => 120,
            'EGFF_GROUP_4' => 120,
            'EGFF_GROUP_5' => 120,
            'EGFF_GROUP_6' => 120,
        ],
        'EGFF_GROUP_2' => [
            'EGFF_GROUP_3' => 60,
            'EGFF_GROUP_4' => 120,
            'EGFF_GROUP_5' => 120,
            'EGFF_GROUP_6' => 120,
        ],
        'EGFF_GROUP_3' => [
            'EGFF_GROUP_1' => -60,
            'EGFF_GROUP_4' => 60,
            'EGFF_GROUP_5' => 120,
            'EGFF_GROUP_6' => 120,
        ],
        'EGFF_GROUP_4' => [
            'EGFF_GROUP_1' => -60,
            'EGFF_GROUP_2' => -60,
            'EGFF_GROUP_5' => 60,
            'EGFF_GROUP_6' => 120,
        ],
        'EGFF_GROUP_5' => [
            'EGFF_GROUP_1' => -60,
            'EGFF_GROUP_2' => -60,
            'EGFF_GROUP_3' => -60,
            'EGFF_GROUP_6' => 60,
        ],
        'EGPH_GROUP_1' => [
            'EGPH_GROUP_2' => 60,
            'EGPH_GROUP_3' => 120,
            'EGPH_GROUP_4' => 180,
            'EGPH_GROUP_5' => 240,
            'EGPH_GROUP_6' => 300,
        ],
        'EGPH_GROUP_2' => [
            'EGPH_GROUP_3' => 60,
            'EGPH_GROUP_4' => 120,
            'EGPH_GROUP_5' => 180,
            'EGPH_GROUP_6' => 240,
        ],
        'EGPH_GROUP_3' => [
            'EGPH_GROUP_1' => -60,
            'EGPH_GROUP_4' => 60,
            'EGPH_GROUP_5' => 120,
            'EGPH_GROUP_6' => 180,
        ],
        'EGPH_GROUP_4' => [
            'EGPH_GROUP_1' => -60,
            'EGPH_GROUP_2' => -60,
            'EGPH_GROUP_5' => 120,
            'EGPH_GROUP_6' => 180,
        ],
        'EGPH_GROUP_5' => [
            'EGPH_GROUP_1' => -60,
            'EGPH_GROUP_2' => -60,
            'EGPH_GROUP_3' => -60,
            'EGPH_GROUP_6' => 180,
        ],
        'EGPH_GROUP_6' => [
            'EGPH_GROUP_1' => -60,
            'EGPH_GROUP_2' => -60,
            'EGPH_GROUP_3' => -60,
            'EGPH_GROUP_4' => -60,
        ],
        'EGPF_GROUP_0' => [
            'EGPF_GROUP_0' => 60,
            'EGPF_GROUP_1' => 120,
            'EGPF_GROUP_2' => 180,
            'EGPF_GROUP_3' => 240,
            'EGPF_GROUP_4' => 300,
        ],
        'EGPF_GROUP_1' => [
            'EGPF_GROUP_2' => 60,
            'EGPF_GROUP_3' => 120,
            'EGPF_GROUP_4' => 180,
        ],
        'EGPF_GROUP_2' => [
            'EGPF_GROUP_3' => 60,
            'EGPF_GROUP_4' => 120,
        ],
        'EGPF_GROUP_3' => [
            'EGPF_GROUP_4' => 60,
        ],
    ];

    /**
     * Lead group => follow group => set interval to
     */
    const SET_INTERVAL_GROUPS = [
        'EGGW_GROUP_2' => [
            'EGGW_GROUP_0' => 60,
        ],
        'EGGW_GROUP_3' => [
            'EGGW_GROUP_0' => 60,
            'EGGW_GROUP_1' => 60,
        ],
        'EGGW_GROUP_4' => [
            'EGGW_GROUP_0' => 60,
            'EGGW_GROUP_1' => 60,
            'EGGW_GROUP_2' => 60,
        ],
        'EGCC_GROUP_2' => [
            'EGCC_GROUP_0' => 60,
        ],
        'EGCC_GROUP_3' => [
            'EGCC_GROUP_0' => 60,
            'EGCC_GROUP_1' => 60,
        ],
        'EGCC_GROUP_4' => [
            'EGCC_GROUP_0' => 60,
            'EGCC_GROUP_1' => 60,
            'EGCC_GROUP_2' => 60,
        ],
        'EGGP_GROUP_2' => [
            'EGCC_GROUP_0' => 60,
        ],
        'EGGP_GROUP_3' => [
            'EGCC_GROUP_0' => 60,
            'EGCC_GROUP_1' => 60,
        ],
        'EGGP_GROUP_4' => [
            'EGCC_GROUP_0' => 60,
            'EGCC_GROUP_1' => 60,
            'EGCC_GROUP_2' => 60,
        ],
        'EGGD_GROUP_4' => [
            'EGGD_GROUP_2' => 60,
            'EGGD_GROUP_1' => 60,
        ],
        'EGGD_GROUP_3' => [
            'EGGD_GROUP_1' => 60,
        ],
        'EGPF_GROUP_2' => [
            'EGPF_GROUP_0' => 60,
        ],
        'EGPF_GROUP_3' => [
            'EGPF_GROUP_0' => 60,
            'EGPF_GROUP_1' => 60,
        ],
        'EGPF_GROUP_4' => [
            'EGPF_GROUP_0' => 60,
            'EGPF_GROUP_1' => 60,
            'EGPF_GROUP_2' => 60,
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
                    'set_interval_to' => null,
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
                    'penalty' => null,
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
