<?php

use App\Models\Airfield\Airfield;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AirfieldTableSeeder extends Seeder
{
    public function run()
    {
        Airfield::create(
            [
                'code' => 'EGLL',
                'latitude' => 51.4775,
                'longitude' => -0.461389,
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGLL']),
                'created_at' => Carbon::now()->subHour(),
                'updated_at' => Carbon::now(),
            ]
        );
        Airfield::create(
            [
                'code' => 'EGBB',
                'latitude' => 52.453889,
                'longitude' => -1.748056,
                'transition_altitude' => 6000,
                'standard_high' => false,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGBB']),
                'created_at' => Carbon::now()->subHour(),
                'updated_at' => Carbon::now(),
            ]
        );
        Airfield::create(
            [
                'code' => 'EGKR',
                'latitude' => 51.213611,
                'longitude' => -0.138611,
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => null,
                'created_at' => Carbon::now()->subHour(),
                'updated_at' => Carbon::now(),
            ]
        );

        Airfield::findOrFail(1)->prenotePairings()
            ->attach(1, ['destination_airfield_id' => 2, 'prenote_id' => 1]);
    }
}
