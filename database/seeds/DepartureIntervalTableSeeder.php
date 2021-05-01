<?php

use App\Models\Departure\SidDepartureIntervalGroup;
use Illuminate\Database\Seeder;

class DepartureIntervalTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SidDepartureIntervalGroup::create(
            [
                'key' => 'GROUP_ONE',
            ]
        );

        SidDepartureIntervalGroup::create(
            [
                'key' => 'GROUP_TWO',
            ]
        );

        SidDepartureIntervalGroup::create(
            [
                'key' => 'GROUP_THREE',
            ]
        );
    }
}
