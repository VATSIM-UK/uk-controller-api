<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ImportAirlines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $airlineFile = fopen(__DIR__ . '/../data/airlines.csv', 'r');
        $airlines = [];
        while (($airline = fgetcsv($airlineFile)) !== false) {
            $airlines[] = [
                'icao_code' => $airline[1],
                'name' => $airline[0],
                'callsign' => $airline[2],
                'created_at' => Carbon::now(),
            ];
        }
        DB::table('airlines')->insert($airlines);
        fclose($airlineFile);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('airlines')->delete();
    }
}
