<?php

use App\Models\Hold\HoldProfileHold;
use Illuminate\Database\Seeder;

class HoldProfileHoldTableSeeder extends Seeder
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
                'hold_profile_id' => 1,
                'hold_id' => 1,
            ],
            [
                'hold_profile_id' => 1,
                'hold_id' => 2,
            ],
            [
                'hold_profile_id' => 2,
                'hold_id' => 2,
            ],
        ];

        HoldProfileHold::insert($holds);
    }
}
