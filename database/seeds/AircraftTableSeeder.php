<?php

use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use App\Models\Aircraft\WakeCategoryScheme;
use Illuminate\Database\Seeder;

class AircraftTableSeeder extends Seeder
{
    public function run()
    {
        $ukScheme = WakeCategoryScheme::where('key', 'UK')
            ->firstOrFail()
            ->id;

        $recatScheme = WakeCategoryScheme::where('key', 'RECAT_EU')
            ->firstOrFail()
            ->id;

        $b738 = Aircraft::create(
            [
                'code' => 'B738',
                'allocate_stands' => true,
                'aerodrome_reference_code' => 'C',
                'wingspan' => 117.83,
                'length' => 129.50,
            ]
        );
        $b738->wakeCategories()->sync(
            [
                WakeCategory::where('code', 'LM')->where('wake_category_scheme_id', $ukScheme)->firstOrFail()->id,
                WakeCategory::where('code', 'M')->where('wake_category_scheme_id', $recatScheme)->firstOrFail()->id,
            ]
        );

        $a333 = Aircraft::create(
            [
                'code' => 'A333',
                'allocate_stands' => true,
                'aerodrome_reference_code' => 'E',
                'wingspan' => 197.83,
                'length' => 208.99
            ],
        );
        $a333->wakeCategories()->sync(
            [
                WakeCategory::where('code', 'H')->where('wake_category_scheme_id', $ukScheme)->firstOrFail()->id,
                WakeCategory::where('code', 'H')->where('wake_category_scheme_id', $recatScheme)->firstOrFail()->id,
            ]
        );
    }
}
