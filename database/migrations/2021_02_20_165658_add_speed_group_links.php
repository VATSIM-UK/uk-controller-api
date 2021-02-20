<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddSpeedGroupLinks extends Migration
{
    /*
     * Lead group => follow group => penalty
     */
    const GROUP_LINKS = [
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
            'EGGW_GROUP_0' => -1,
            'EGGW_GROUP_3' => 1,
            'EGGW_GROUP_4' => 2,
        ],
        'EGGW_GROUP_3' => [
            'EGGW_GROUP_0' => -1,
            'EGGW_GROUP_1' => -1,
            'EGGW_GROUP_4' => 1,
        ],
        'EGGW_GROUP_4' => [
            'EGGW_GROUP_0' => -1,
            'EGGW_GROUP_1' => -1,
            'EGGW_GROUP_2' => -1,
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
            'EGCC_GROUP_0' => -1,
            'EGCC_GROUP_3' => 2,
            'EGCC_GROUP_4' => 3,
        ],
        'EGCC_GROUP_3' => [
            'EGCC_GROUP_0' => -1,
            'EGCC_GROUP_1' => -1,
            'EGCC_GROUP_4' => 3,
        ],
        'EGCC_GROUP_4' => [
            'EGCC_GROUP_0' => -1,
            'EGCC_GROUP_1' => -1,
            'EGCC_GROUP_2' => -1,
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
            'EGGP_GROUP_0' => -1,
            'EGGP_GROUP_3' => 1,
            'EGGP_GROUP_4' => 2,
        ],
        'EGGP_GROUP_3' => [
            'EGGP_GROUP_0' => -1,
            'EGGP_GROUP_1' => -1,
            'EGGP_GROUP_4' => 1,
        ],
        'EGGP_GROUP_4' => [
            'EGGP_GROUP_0' => -1,
            'EGGP_GROUP_1' => -1,
            'EGGP_GROUP_2' => -1,
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
        foreach (self::GROUP_LINKS as $leadGroup => $followingGroups) {
            foreach ($followingGroups as $followingGroup => $penalty) {
                $mergedLinks[] = [
                    'lead_speed_group_id' => $groups[$leadGroup],
                    'follow_speed_group_id' => $groups[$followingGroup],
                    'penalty' => $penalty,
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
