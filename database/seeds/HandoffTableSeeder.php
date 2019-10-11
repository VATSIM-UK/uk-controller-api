<?php

use App\Models\Controller\Handoff;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HandoffTableSeeder extends Seeder
{
    public function run()
    {
        Handoff::insert(
            [
                [
                    'key' => 'HANDOFF_ORDER_1',
                    'description' => 'foo',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'HANDOFF_ORDER_2',
                    'description' => 'foo',
                    'created_at' => Carbon::now(),
                ],
            ]
        );

        Handoff::find(1)->controllers()->attach(
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

        Handoff::find(2)->controllers()->attach(
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
