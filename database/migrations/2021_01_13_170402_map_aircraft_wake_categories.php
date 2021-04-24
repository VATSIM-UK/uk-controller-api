<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MapAircraftWakeCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $allAircraft = DB::table('aircraft')->get();
        $recatCategories = DB::table('recat_categories')->get()->mapWithKeys(function ($category) {
            return [$category->id => $category->code];
        });
        $newCategories = DB::table('wake_categories')->get()->mapWithKeys(function ($category) {
            return [$category->code => $category->id];
        });

        $formattedAircraft = [];
        foreach ($allAircraft as $aircraft) {
            $formattedAircraft[] = [
                'aircraft_id' => $aircraft->id,
                'wake_category_id' => $aircraft->wake_category_id,
                'created_at' => Carbon::now(),
            ];
            if ($aircraft->recat_category_id) {
                $formattedAircraft[] = [
                    'aircraft_id' => $aircraft->id,
                    'wake_category_id' => $newCategories[$recatCategories[$aircraft->recat_category_id]],
                    'created_at' => Carbon::now(),
                ];
            }
        }

        DB::table('aircraft_wake_category')->insert($formattedAircraft);
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
