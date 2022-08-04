<?php


use App\Models\Airfield\Airfield;
use App\Models\Controller\ControllerPosition;
use Illuminate\Database\Seeder;

class ControllerPositionsTableSeeder extends Seeder
{
    public function run()
    {
        ControllerPosition::insert(
            [
                [
                    'callsign' => 'EGLL_S_TWR',
                    'frequency' => 118.500,
                    'requests_departure_releases' => true,
                    'receives_departure_releases' => false,
                    'sends_prenotes' => true,
                    'receives_prenotes' => false,
                ],
                [
                    'callsign' => 'EGLL_N_APP',
                    'requests_departure_releases' => true,
                    'receives_departure_releases' => true,
                    'sends_prenotes' => true,
                    'receives_prenotes' => true,
                    'frequency' => 119.725,
                ],
                [
                    'callsign' => 'LON_S_CTR',
                    'frequency' => 129.425,
                    'requests_departure_releases' => true,
                    'receives_departure_releases' => true,
                    'sends_prenotes' => true,
                    'receives_prenotes' => true,
                ],
                [
                    'callsign' => 'LON_C_CTR',
                    'frequency' => 127.100,
                    'requests_departure_releases' => false,
                    'receives_departure_releases' => false,
                    'sends_prenotes' => false,
                    'receives_prenotes' => false,
                ],
            ]
        );

        Airfield::where('code', 'EGLL')->firstOrFail()->controllers()->attach(
            [
                [
                    'controller_position_id' => 1,
                    'order' => 1,
                ],
                [
                    'controller_position_id' => 2,
                    'order' => 2,
                ],
                [
                    'controller_position_id' => 3,
                    'order' => 3,
                ],
            ]
        );

        Airfield::where('code', 'EGBB')->firstOrFail()->controllers()->attach(
            [
                [
                    'controller_position_id' => 4,
                    'order' => 1,
                ],
            ]
        );
    }
}
