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
                    'relative_weighting' => 0,
                ],
                [
                    'code' => 'S',
                    'description' => 'Small',
                    'relative_weighting' => 5,
                ],
                [
                    'code' => 'LM',
                    'description' => 'Lower Medium',
                    'relative_weighting' => 10,
                ],
                [
                    'code' => 'UM',
                    'description' => 'Upper Medium',
                    'relative_weighting' => 15,
                ],
                [
                    'code' => 'H',
                    'description' => 'Heavy',
                    'relative_weighting' => 20,
                ],
                [
                    'code' => 'J',
                    'description' => 'Jumbo',
                    'relative_weighting' => 25,
                ],
            ]
        );

        Aircraft::insert(
            [
                [
                    'code' => 'B738',
                    'wake_category_id' => WakeCategory::where('code', 'LM')->firstOrFail()->id,
                    'allocate_stands' => true,
                    'wingspan' => 117.83,
                    'length' => 129.50,
                ],
                [
                    'code' => 'A333',
                    'wake_category_id' => WakeCategory::where('code', 'H')->firstOrFail()->id,
                    'allocate_stands' => true,
                    'wingspan' => 197.83,
                    'length' => 208.99
                ],
            ]
        );
    }
}
