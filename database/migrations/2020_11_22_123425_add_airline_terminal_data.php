<?php

use App\Models\Airfield\Terminal;
use App\Models\Airline\Airline;
use Illuminate\Database\Migrations\Migration;

class AddAirlineTerminalData extends Migration
{
    const AIRLINE_TERMINAL_FILE = __DIR__ . '/../data/stands/2020/terminalairlines.csv';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $pairs = fopen(self::AIRLINE_TERMINAL_FILE, 'r');
        while ($line = fgetcsv($pairs)) {
            $airline =             Airline::where('icao_code', $line[1])
                ->first();

            if (!$airline) {
                dd($line);
            }
            Airline::where('icao_code', $line[1])
                ->first()
                ->terminals()
                ->attach(Terminal::where('key', $line[0])->first()->id);
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
