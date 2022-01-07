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
                    'runway_id' => 1,
                    'initial_altitude' => 3000,
                    'handoff_id' => 1,
                ],
                [
                    'id' => 2,
                    'identifier' => 'TEST1Y',
                    'runway_id' => 2,
                    'initial_altitude' => 4000,
                    'handoff_id' => 1,
                ],
                [
                    'id' => 3,
                    'identifier' => 'TEST1A',
                    'runway_id' => 3,
                    'initial_altitude' => 5000,
                    'handoff_id' => 2,
                ],
            ]
        );

        Sid::find(1)->prenotes()->attach(
            [
                'prenote_id' => 1,
            ]
        );
    }
}
