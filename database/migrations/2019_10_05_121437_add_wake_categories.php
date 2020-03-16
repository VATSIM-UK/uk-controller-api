<?php

use App\Models\Aircraft\WakeCategory;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddWakeCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        WakeCategory::insert(
            [
                [
                    'code' => 'L',
                    'description' => 'Light',
                    'created_at' => Carbon::now(),
                ],
                [
                    'code' => 'S',
                    'description' => 'Small',
                    'created_at' => Carbon::now(),
                ],
                [
                    'code' => 'LM',
                    'description' => 'Lower Medium',
                    'created_at' => Carbon::now(),
                ],
                [
                    'code' => 'UM',
                    'description' => 'Upper Medium',
                    'created_at' => Carbon::now(),
                ],
                [
                    'code' => 'H',
                    'description' => 'Heavy',
                    'created_at' => Carbon::now(),
                ],
                [
                    'code' => 'J',
                    'description' => 'Jumbo',
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
        $categories = WakeCategory::all();
        $categories->each(function (WakeCategory $category) {
            $category->delete();
        });
    }
}
