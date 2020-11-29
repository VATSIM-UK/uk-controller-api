<?php

use App\Models\Aircraft\Aircraft;
use Illuminate\Database\Migrations\Migration;

class AddAircraftDimensions extends Migration
{
    const DIMENSIONS_FILE = __DIR__ . '/../data/stands/2020/aircraftdimensions.csv';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dimensions = fopen(self::DIMENSIONS_FILE, 'r');
        while ($line = fgetcsv($dimensions)) {
            Aircraft::where('code', $line[0])
                ->update(['wingspan' => $line[1], 'length' => $line[2]]);
        }
        fclose($dimensions);
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
