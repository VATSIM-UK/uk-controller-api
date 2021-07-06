<?php

namespace Database\Factories\Airfield;

use Faker\Generator;
use Illuminate\Support\Str;

trait UsesAirfieldIcaoCodes
{
    private function getAirfieldIcao(Generator $faker): string
    {
        return Str::upper($faker->unique()->lexify('EG??'));
    }
}
