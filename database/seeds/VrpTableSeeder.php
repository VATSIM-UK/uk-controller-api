<?php

use App\Models\Airfield\VisualReferencePoint;
use Illuminate\Database\Seeder;

class VrpTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vrp1 = VisualReferencePoint::create(
            [
                'name' => 'VRP One',
                'short_name' => 'V1',
                'latitude' => 1,
                'longitude' => 2,
            ]
        );
        $vrp1->airfields()->sync([1, 2]);

        VisualReferencePoint::create(
            [
                'name' => 'VRP Two',
                'short_name' => 'V2',
                'latitude' => 3,
                'longitude' => 4,
            ]
        );
    }
}
