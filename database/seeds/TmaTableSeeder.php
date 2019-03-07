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
                'created_at' => Carbon::now()->subHour(),
                'updated_at' => Carbon::now(),
            ]
        );
    }
}
