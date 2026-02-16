<?php

use App\Models\SmrArea;
use Illuminate\Database\Seeder;

class SmrAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SmrArea::create([
            'airfield_id' => 1,
            'coordinates' => str_repeat("COORD:N000.00.00.000:E000.00.00.000\n", 3),
            'start_date'  => null,
            'end_date'    => null,
        ]);
    }
}
