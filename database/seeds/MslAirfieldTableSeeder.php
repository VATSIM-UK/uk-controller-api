<?php

use App\Models\MinStack\MslAirfield;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MslAirfieldTableSeeder extends Seeder
{
    public function run()
    {
        MslAirfield::create(
            [
                'airfield_id' => 1,
                'msl' => 7000,
                'generated_at' => Carbon::now()->subHour(),
            ]
        );
    }
}
