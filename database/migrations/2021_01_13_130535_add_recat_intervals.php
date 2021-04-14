<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddRecatIntervals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $categories = DB::table('wake_categories')->get()->mapWithKeys(
            function ($category) {
                return [$category->code => $category->id];
            }
        )->toArray();

        DB::table('departure_wake_intervals')->insert(
            [
                // CAT-A lead
                [
                    'lead_wake_category_id' => $categories['A'],
                    'following_wake_category_id' => $categories['B'],
                    'interval' => 100,
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['A'],
                    'following_wake_category_id' => $categories['C'],
                    'interval' => 120,
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['A'],
                    'following_wake_category_id' => $categories['D'],
                    'interval' => 140,
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['A'],
                    'following_wake_category_id' => $categories['E'],
                    'interval' => 160,
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['A'],
                    'following_wake_category_id' => $categories['F'],
                    'interval' => 180,
                    'intermediate' => false,
                ],

                // CAT-B lead
                [
                    'lead_wake_category_id' => $categories['B'],
                    'following_wake_category_id' => $categories['D'],
                    'interval' => 100,
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['B'],
                    'following_wake_category_id' => $categories['E'],
                    'interval' => 120,
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['B'],
                    'following_wake_category_id' => $categories['F'],
                    'interval' => 140,
                    'intermediate' => false,
                ],

                // CAT-C lead
                [
                    'lead_wake_category_id' => $categories['C'],
                    'following_wake_category_id' => $categories['D'],
                    'interval' => 80,
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['C'],
                    'following_wake_category_id' => $categories['E'],
                    'interval' => 100,
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['C'],
                    'following_wake_category_id' => $categories['F'],
                    'interval' => 80,
                    'intermediate' => false,
                ],

                // CAT-D Lead
                [
                    'lead_wake_category_id' => $categories['D'],
                    'following_wake_category_id' => $categories['F'],
                    'interval' => 120,
                    'intermediate' => false,
                ],

                // CAT-E Lead
                [
                    'lead_wake_category_id' => $categories['E'],
                    'following_wake_category_id' => $categories['F'],
                    'interval' => 100,
                    'intermediate' => false,
                ],

                // CAT-F Lead
                [
                    'lead_wake_category_id' => $categories['F'],
                    'following_wake_category_id' => $categories['F'],
                    'interval' => 80,
                    'intermediate' => false,
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
