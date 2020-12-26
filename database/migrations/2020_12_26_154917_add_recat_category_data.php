<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddRecatCategoryData extends Migration
{
    private const DATA_FILE = __DIR__ . '/../data/recat/categories.csv';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $categories = DB::table('recat_categories')->get()->mapWithKeys(function ($category) {
            return [$category->code => $category->id];
        });

        $file = fopen(self::DATA_FILE, 'r+');
        while ($line = fgetcsv($file))
        {
            DB::table('aircraft')
                ->where('code', $line[0])
                ->update(['recat_category_id' => $categories[$line[1]], 'updated_at' => Carbon::now()]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Nothing to do
    }
}
