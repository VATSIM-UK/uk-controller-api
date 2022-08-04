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
                    'description' => 'foo',
                    'created_at' => Carbon::now(),
                ],
                [
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
