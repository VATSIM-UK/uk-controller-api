<?php

use App\Models\Squawks\Allocation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SquawkAllocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Allocation::insert(
            [
                [
                    'id' => 1,
                    'callsign' => 'BAW123',
                    'squawk' => '4723',
                    'allocated_by' => 1203533,
                    'allocated_at' => Carbon::now(),
                ],
                [
                    'id' => 2,
                    'callsign' => 'BAW456',
                    'squawk' => '2321',
                    'allocated_by' => 1203533,
                    'allocated_at' => Carbon::now()->subMinutes(config('squawk.allocation_min') - 1),
                ],
                [
                    'id' => 3,
                    'callsign' => 'NAX1431',
                    'squawk' => '4325',
                    'allocated_by' => 1203533,
                    'allocated_at' => Carbon::now()->subMinutes(config('squawk.allocation_min')),
                ],
                [
                    'id' => 4,
                    'callsign' => 'TOM43E',
                    'squawk' => '5436',
                    'allocated_by' => 1203533,
                    'allocated_at' => Carbon::now()->subMinutes(config('squawk.allocation_min') + 1),
                ],
            ]
        );
    }
}
