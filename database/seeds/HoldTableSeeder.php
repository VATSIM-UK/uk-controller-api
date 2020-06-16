<?php

use App\Models\Hold\Hold;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HoldTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $holds = [
            [
                'navaid_id' => 1,
                'inbound_heading' => 285,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'left',
                'description' => 'WILLO',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'navaid_id' => 2,
                'inbound_heading' => 309,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'right',
                'description' => 'TIMBA',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'navaid_id' => 3,
                'inbound_heading' => 90,
                'minimum_altitude' => 3000,
                'maximum_altitude' => 5000,
                'turn_direction' => 'right',
                'description' => 'Mayfield Low',
                'created_at' => Carbon::now()->toDateTimeString(),
            ]
        ];

        Hold::insert($holds);
    }
}
