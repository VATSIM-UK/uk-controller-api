<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddRecatCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('recat_categories')->insert(
            [
                [
                    'code' => 'A',
                    'description' => 'Super Heavy',
                    'created_at' => Carbon::now(),
                ],
                [
                    'code' => 'B',
                    'description' => 'Upper Heavy',
                    'created_at' => Carbon::now(),
                ],
                [
                    'code' => 'C',
                    'description' => 'Lower Heavy',
                    'created_at' => Carbon::now(),
                ],
                [
                    'code' => 'D',
                    'description' => 'Upper Medium',
                    'created_at' => Carbon::now(),
                ],
                [
                    'code' => 'E',
                    'description' => 'Lower Medium',
                    'created_at' => Carbon::now(),
                ],
                [
                    'code' => 'F',
                    'description' => 'Light',
                    'created_at' => Carbon::now(),
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
    }
}
