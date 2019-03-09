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
                'tma_id' => 2,
                'msl' => 6000,
                'generated_at' => Carbon::now()->subHour(),
            ]
        );
    }
}
