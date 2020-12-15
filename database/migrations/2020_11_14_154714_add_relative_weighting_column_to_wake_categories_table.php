<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddRelativeWeightingColumnToWakeCategoriesTable extends Migration
{
    private const WEIGHTINGS = [
        'L' => 0,
        'S' => 5,
        'LM' => 10,
        'UM' => 15,
        'H' => 20,
        'J' => 25,
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wake_categories', function (Blueprint $table) {
            $table->unsignedTinyInteger('relative_weighting')
                ->after('description')
                ->comment('Represents how the categories are relative to each other');
        });

        // Add the weightings
        foreach (self::WEIGHTINGS as $code => $weighting) {
            DB::table('wake_categories')
                ->where('code', $code)
                ->update(['relative_weighting' => $weighting]);
        }

        Schema::table('wake_categories', function (Blueprint $table) {
            $table->unique('relative_weighting');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wake_categories', function (Blueprint $table) {
            $table->dropUnique('wake_categories_relative_weighting_unique');
            $table->dropColumn('relative_weighting');
        });
    }
}
