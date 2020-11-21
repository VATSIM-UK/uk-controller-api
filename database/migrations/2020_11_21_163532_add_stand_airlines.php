<?php

use App\Models\Airfield\Airfield;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use Illuminate\Database\Migrations\Migration;

class AddStandAirlines extends Migration
{
    const STAND_AIRLINES_FILE = __DIR__ . '/../data/stands/2020/standairlines.csv';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $pairs = fopen(self::STAND_AIRLINES_FILE, 'r');
        while ($line = fgetcsv($pairs)) {
            $airfieldId = Airfield::where('code', $line[0])->first()->id;
            $stand = Stand::where('airfield_id', $airfieldId)
                ->where('identifier', $line[1])
                ->first();

            $airlineId = Airline::where('icao_code', $line[2])
                ->first()
                ->id;

            $stand->airlines()->attach([$airlineId => ['destination' => $line[3] ? $line[3] : null]]);
        }
        fclose($pairs);
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
