<?php

use App\Models\Airline\Airline;
use Illuminate\Database\Seeder;

class AirlineTableSeeder extends Seeder
{
    public function run()
    {
        $baw = Airline::create(
            [
                'icao_code' => 'BAW',
                'name' => 'British Airways',
                'callsign' => 'SPEEDBIRD'
            ]
        );
        Airline::create(
            [
                'icao_code' => 'SHT',
                'name' => 'British Airways Shuttle',
                'callsign' => 'SHUTTLE'
            ]
        );
        Airline::create(
            [
                'icao_code' => 'VIR',
                'name' => 'Virgin Atlantic Airways',
                'callsign' => 'VIR'
            ]
        );

        // Seed pivot data required for Filament relation manager tests
        $baw->terminals()->attach(1, ['priority' => 100]);
        $baw->stands()->attach(1, ['priority' => 100]);
    }
}
