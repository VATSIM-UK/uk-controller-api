<?php

use App\Models\MinStack\TmaMslCalculation;
use Illuminate\Database\Seeder;

class TmaMslCalculationTableSeeder extends Seeder
{
    public function run()
    {
        TmaMslCalculation::create(
            [
                'tma_id' => 1,
                'calculation' => json_encode(['type' => 'airfield', 'code' => 'EGLL']),
            ]
        );
    }
}
