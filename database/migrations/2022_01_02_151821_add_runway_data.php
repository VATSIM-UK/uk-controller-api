<?php

use App\Helpers\Sectorfile\Coordinate;
use App\Services\RunwayService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddRunwayData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $file = fopen(__DIR__ . '/../data/runway/2022-runways.csv', 'r+');
        while (($runwayLine = fgets($file)) !== false) {
            $exploded = explode(' ', $runwayLine);
            RunwayService::addRunwayPair(
                $exploded[8],
                $exploded[0],
                $exploded[2],
                new Coordinate($exploded[4], $exploded[5]),
                $exploded[1],
                $exploded[3],
                new Coordinate($exploded[6], $exploded[7])
            );
        }
        fclose($file);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('runways')->delete();
    }
}
