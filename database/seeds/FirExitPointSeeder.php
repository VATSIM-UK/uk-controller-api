<?php

use App\Models\IntentionCode\FirExitPoint;
use Illuminate\Database\Seeder;

class FirExitPointSeeder extends Seeder
{
    public function run()
    {
        FirExitPoint::create(
            [
                'exit_point' => 'FOO',
                'internal' => true,
                'exit_direction_start' => 123,
                'exit_direction_end' => 234,
            ]
        );
    }
}
