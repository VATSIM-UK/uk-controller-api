<?php

use App\Models\Airfield;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AirfieldTableSeeder extends Seeder
{
    public function run()
    {
        Airfield::create(
            [
                'code' => 'EGLL',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'airfield', 'code' => 'EGLL']),
                'created_at' => Carbon::now()->subHour(),
                'updated_at' => Carbon::now(),
            ]
        );
        Airfield::create(
            [
                'code' => 'EGBB',
                'transition_altitude' => 6000,
                'standard_high' => false,
                'msl_calculation' => json_encode(['type' => 'airfield', 'code' => 'EGBB']),
                'created_at' => Carbon::now()->subHour(),
                'updated_at' => Carbon::now(),
            ]
        );
    }
}
