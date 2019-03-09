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
                'created_at' => Carbon::now()->subHour(),
                'updated_at' => Carbon::now(),
            ]
        );
        Airfield::create(
            [
                'code' => 'EGBB',
                'transition_altitude' => 6000,
                'created_at' => Carbon::now()->subHour(),
                'updated_at' => Carbon::now(),
            ]
        );
    }
}
