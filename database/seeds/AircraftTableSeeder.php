<?php

use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use Illuminate\Database\Seeder;

class AircraftTableSeeder extends Seeder
{
    public function run()
    {
        $b738 = Aircraft::create(
            [
                'code' => 'B738',
                'allocate_stands' => true,
                'wingspan' => 117.83,
                'length' => 129.50,
            ]
        );
        $b738->wakeCategories()->sync(
            [
                WakeCategory::where('code', 'LM')->firstOrFail()->id,
                WakeCategory::where('code', 'D')->firstOrFail()->id,
            ]
        );

        $a333 = Aircraft::create(
            [
                'code' => 'A333',
                'allocate_stands' => true,
                'wingspan' => 197.83,
                'length' => 208.99
            ],
        );
        $a333->wakeCategories()->sync(
            [
                WakeCategory::where('code', 'H')->firstOrFail()->id,
                WakeCategory::where('code', 'B')->firstOrFail()->id,
            ]
        );
    }
}
