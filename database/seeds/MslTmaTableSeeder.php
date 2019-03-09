<?php

use App\Models\MinStack\MslTma;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MslTmaTableSeeder extends Seeder
{
    public function run()
    {
        MslTma::create(
            [
                'tma_id' => 1,
                'msl' => 8000,
                'generated_at' => Carbon::now()->subHour(),
            ]
        );
    }
}
