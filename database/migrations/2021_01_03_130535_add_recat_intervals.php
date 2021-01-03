<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecatIntervals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $categories = DB::table('recat_categories')->get()->mapWithKeys(
            function ($category) {
                return [$category->code => $category->id];
            }
        )->toArray();

        DB::table('departure_recat_wake_intervals')->insert(
            [
                // CAT-A lead
                [
                    'lead_recat_category_id' => $categories['A'],
                    'following_recat_category_id' => $categories['B'],
                    'interval' => 100,
                ],
                [
                    'lead_recat_category_id' => $categories['A'],
                    'following_recat_category_id' => $categories['C'],
                    'interval' => 120,
                ],
                [
                    'lead_recat_category_id' => $categories['A'],
                    'following_recat_category_id' => $categories['D'],
                    'interval' => 140,
                ],
                [
                    'lead_recat_category_id' => $categories['A'],
                    'following_recat_category_id' => $categories['E'],
                    'interval' => 160,
                ],
                [
                    'lead_recat_category_id' => $categories['A'],
                    'following_recat_category_id' => $categories['F'],
                    'interval' => 180,
                ],

                // CAT-B lead
                [
                    'lead_recat_category_id' => $categories['B'],
                    'following_recat_category_id' => $categories['D'],
                    'interval' => 100,
                ],
                [
                    'lead_recat_category_id' => $categories['B'],
                    'following_recat_category_id' => $categories['E'],
                    'interval' => 120,
                ],
                [
                    'lead_recat_category_id' => $categories['B'],
                    'following_recat_category_id' => $categories['F'],
                    'interval' => 140,
                ],

                // CAT-C lead
                [
                    'lead_recat_category_id' => $categories['C'],
                    'following_recat_category_id' => $categories['D'],
                    'interval' => 80,
                ],
                [
                    'lead_recat_category_id' => $categories['C'],
                    'following_recat_category_id' => $categories['E'],
                    'interval' => 100,
                ],
                [
                    'lead_recat_category_id' => $categories['C'],
                    'following_recat_category_id' => $categories['F'],
                    'interval' => 80,
                ],

                // CAT-D Lead
                [
                    'lead_recat_category_id' => $categories['D'],
                    'following_recat_category_id' => $categories['F'],
                    'interval' => 120,
                ],

                // CAT-E Lead
                [
                    'lead_recat_category_id' => $categories['E'],
                    'following_recat_category_id' => $categories['F'],
                    'interval' => 100,
                ],

                // CAT-F Lead
                [
                    'lead_recat_category_id' => $categories['F'],
                    'following_recat_category_id' => $categories['F'],
                    'interval' => 80,
                ],
            ]
        );
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
