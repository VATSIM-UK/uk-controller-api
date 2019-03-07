<?php

use App\Models\AirfieldMslCalculation;
use Illuminate\Database\Seeder;

class AirfieldMslCalculationTableSeeder extends Seeder
{
    public function run()
    {
        AirfieldMslCalculation::create(
            [
                'airfield_id' => 1,
                'calculation' => json_encode(['type' => 'airfield', 'code' => 'EGLL']),
            ]
        );
    }
}
