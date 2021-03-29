<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CopyRecatToNewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $recatScheme = DB::table('wake_category_schemes')#
            ->where('key', 'RECAT_EU')
            ->first()
            ->id;

        $recatCategories = DB::table('recat_categories')
            ->get()
            ->toArray();

        $mappedCategories = [];
        foreach ($recatCategories as $key => $recatCategory) {
            unset($recatCategory->id);
            $mappedCategories[] = array_merge(
                (array) $recatCategory,
                [
                    'wake_category_scheme_id' => $recatScheme,
                    'relative_weighting' => $key + 1,
                ]
            );
        }

        DB::table('wake_categories')
            ->insert($mappedCategories);
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
