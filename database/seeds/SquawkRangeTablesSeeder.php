<?php

use App\Models\Squawk\Orcam\OrcamSquawkRange;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRange;
use Illuminate\Database\Seeder;

class SquawkRangeTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OrcamSquawkRange::create(
            [
                'origin' => 'ED',
                'first' => '0101',
                'last' => '0101'
            ]
        );

        UnitDiscreteSquawkRange::create(
            [
                'unit' => 'EGKK',
                'first' => '0202',
                'last' => '0202'
            ]
        );
    }
}
