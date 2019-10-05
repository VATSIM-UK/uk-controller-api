<?php

use App\Models\Controller\Prenote;
use Illuminate\Database\Seeder;

class PrenoteTableSeeder extends Seeder
{
    public function run()
    {
        Prenote::insert(
            [
                [
                    'key' => 'PRENOTE_ONE',
                    'description' => 'Prenote One',
                ],
                [
                    'key' => 'PRENOTE_TWO',
                    'description' => 'Prenote Two',
                ],
            ]
        );

        Prenote::find(1)->controllers()->attach(
            [
                [
                    'controller_position_id' => 1,
                    'order' => 1,
                ],
                [
                    'controller_position_id' => 2,
                    'order' => 2,
                ],
            ]
        );

        Prenote::find(2)->controllers()->attach(
            [
                [
                    'controller_position_id' => 2,
                    'order' => 1,
                ],
                [
                    'controller_position_id' => 3,
                    'order' => 2,
                ],
            ]
        );
    }
}
