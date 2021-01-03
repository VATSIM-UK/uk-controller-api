<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDepartureIntervalData extends Migration
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

        DB::table('departure_uk_wake_intervals')->insert(
            [
                // A380 vs any
                [
                    'lead_wake_category_id' => $categories['J'],
                    'following_wake_category_id' => $categories['H'],
                    'interval' => '120',
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['J'],
                    'following_wake_category_id' => $categories['H'],
                    'interval' => '180',
                    'intermediate' => true,
                ],
                [
                    'lead_wake_category_id' => $categories['J'],
                    'following_wake_category_id' => $categories['UM'],
                    'interval' => '180',
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['J'],
                    'following_wake_category_id' => $categories['UM'],
                    'interval' => '240',
                    'intermediate' => true,
                ],
                [
                    'lead_wake_category_id' => $categories['J'],
                    'following_wake_category_id' => $categories['LM'],
                    'interval' => '180',
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['J'],
                    'following_wake_category_id' => $categories['LM'],
                    'interval' => '240',
                    'intermediate' => true,
                ],
                [
                    'lead_wake_category_id' => $categories['J'],
                    'following_wake_category_id' => $categories['S'],
                    'interval' => '180',
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['J'],
                    'following_wake_category_id' => $categories['S'],
                    'interval' => '240',
                    'intermediate' => true,
                ],
                [
                    'lead_wake_category_id' => $categories['J'],
                    'following_wake_category_id' => $categories['L'],
                    'interval' => '180',
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['J'],
                    'following_wake_category_id' => $categories['L'],
                    'interval' => '240',
                    'intermediate' => true,
                ],

                // Heavy vs Heavy and below
                [
                    'lead_wake_category_id' => $categories['H'],
                    'following_wake_category_id' => $categories['H'],
                    'interval' => '80',
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['H'],
                    'following_wake_category_id' => $categories['H'],
                    'interval' => '80',
                    'intermediate' => true,
                ],
                [
                    'lead_wake_category_id' => $categories['H'],
                    'following_wake_category_id' => $categories['UM'],
                    'interval' => '120',
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['H'],
                    'following_wake_category_id' => $categories['UM'],
                    'interval' => '180',
                    'intermediate' => true,
                ],
                [
                    'lead_wake_category_id' => $categories['H'],
                    'following_wake_category_id' => $categories['LM'],
                    'interval' => '120',
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['H'],
                    'following_wake_category_id' => $categories['LM'],
                    'interval' => '180',
                    'intermediate' => true,
                ],
                [
                    'lead_wake_category_id' => $categories['H'],
                    'following_wake_category_id' => $categories['S'],
                    'interval' => '120',
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['H'],
                    'following_wake_category_id' => $categories['S'],
                    'interval' => '180',
                    'intermediate' => true,
                ],
                [
                    'lead_wake_category_id' => $categories['H'],
                    'following_wake_category_id' => $categories['L'],
                    'interval' => '120',
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['H'],
                    'following_wake_category_id' => $categories['L'],
                    'interval' => '180',
                    'intermediate' => true,
                ],

                // Upper medium vs light
                [
                    'lead_wake_category_id' => $categories['UM'],
                    'following_wake_category_id' => $categories['L'],
                    'interval' => '120',
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['UM'],
                    'following_wake_category_id' => $categories['L'],
                    'interval' => '180',
                    'intermediate' => true,
                ],

                // Lower medium vs light
                [
                    'lead_wake_category_id' => $categories['LM'],
                    'following_wake_category_id' => $categories['L'],
                    'interval' => '120',
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['LM'],
                    'following_wake_category_id' => $categories['L'],
                    'interval' => '180',
                    'intermediate' => true,
                ],

                // Small vs light
                [
                    'lead_wake_category_id' => $categories['S'],
                    'following_wake_category_id' => $categories['L'],
                    'interval' => '120',
                    'intermediate' => false,
                ],
                [
                    'lead_wake_category_id' => $categories['S'],
                    'following_wake_category_id' => $categories['L'],
                    'interval' => '180',
                    'intermediate' => true,
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
        DB::table('departure_wake_intervals')->truncate();
    }
}
