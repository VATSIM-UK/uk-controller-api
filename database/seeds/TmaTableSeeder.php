<?php

use App\Models\Tma;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TmaTableSeeder extends Seeder
{
    public function run()
    {
        Tma::create(
            [
                'name' => 'LTMA',
                'description' => 'London TMA',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_airfield_id' => 1,
                'created_at' => Carbon::now()->subHour(),
                'updated_at' => Carbon::now(),
            ]
        );
        Tma::create(
            [
                'name' => 'MTMA',
                'description' => 'Manchester TMA',
                'transition_altitude' => 5000,
                'msl_airfield_id' => 2,
                'standard_high' => false,
                'created_at' => Carbon::now()->subHour(),
                'updated_at' => Carbon::now(),
            ]
        );
    }
}
