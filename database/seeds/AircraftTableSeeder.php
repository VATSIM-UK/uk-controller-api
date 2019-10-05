<?php

use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use Illuminate\Database\Seeder;

class AircraftTableSeeder extends Seeder
{
    public function run()
    {
        WakeCategory::insert(
            [
                [
                    'code' => 'L',
                    'description' => 'Light',
                ],
                [
                    'code' => 'S',
                    'description' => 'Small',
                ],
                [
                    'code' => 'LM',
                    'description' => 'Lower Medium',
                ],
                [
                    'code' => 'UM',
                    'description' => 'Upper Medium',
                ],
                [
                    'code' => 'H',
                    'description' => 'Heavy',
                ],
                [
                    'code' => 'J',
                    'description' => 'Jumbo',
                ],
            ]
        );

        Aircraft::insert(
            [
                [
                    'code' => 'B738',
                    'wake_category_id' => WakeCategory::where('code', 'LM')->firstOrFail()->id,
                ],
                [
                    'code' => 'A333',
                    'wake_category_id' => WakeCategory::where('code', 'H')->firstOrFail()->id,
                ],
            ]
        );
    }
}
