<?php

use App\Models\Runway\Runway;
use Illuminate\Database\Seeder;

class RunwayTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Runway::insert(
            [
                [
                    'id' => 1,
                    'airfield_id' => 1,
                    'identifier' => '27L',
                    'threshold_latitude' => 1,
                    'threshold_longitude' => 2,
                    'heading' => 270,
                    'glideslope_angle' => 3,
                    'threshold_elevation' => 4,
                ],
                [
                    'id' => 2,
                    'airfield_id' => 1,
                    'identifier' => '09R',
                    'threshold_latitude' => 3,
                    'threshold_longitude' => 4,
                    'heading' => 90,
                    'glideslope_angle' => 4,
                    'threshold_elevation' => 5,
                ],
                [
                    'id' => 3,
                    'airfield_id' => 2,
                    'identifier' => '33',
                    'threshold_latitude' => 5,
                    'threshold_longitude' => 6,
                    'heading' => 330,
                    'glideslope_angle' => 5,
                    'threshold_elevation' => 6,
                ],
            ]
        );

        Runway::find(1)->inverses()->sync(2);
        Runway::find(2)->inverses()->sync(1);
    }
}
