<?php

use App\Models\Sid;
use Illuminate\Database\Seeder;

class SidTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sid::insert(
            [
                [
                    'id' => 1,
                    'identifier' => 'TEST1X',
                    'airfield_id' => 1,
                    'initial_altitude' => 3000,
                ],
                [
                    'id' => 2,
                    'identifier' => 'TEST1Y',
                    'airfield_id' => 1,
                    'initial_altitude' => 4000,
                ],
                [
                    'id' => 3,
                    'identifier' => 'TEST1A',
                    'airfield_id' => 2,
                    'initial_altitude' => 5000,
                ],
            ]
        );
    }
}
