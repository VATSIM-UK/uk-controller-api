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
                ],
                [
                    'callsign' => 'EGLL_N_APP',
                    'frequency' => 119.720,
                ],
                [
                    'callsign' => 'LON_S_CTR',
                    'frequency' => 129.420,
                ],
                [
                    'callsign' => 'LON_C_CTR',
                    'frequency' => 127.100
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
