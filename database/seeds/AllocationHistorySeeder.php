<?php

use App\Models\Squawks\Allocation;
use App\Models\Squawks\AllocationHistory;
use App\Services\SquawkAllocationService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AllocationHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // This one is still valid
        AllocationHistory::create(
            [
                'callsign' => 'NAX123',
                'squawk' => '1423',
                'allocated_by' => UserTableSeeder::ACTIVE_USER_CID,
                'allocated_at' => Carbon::now()
                    ->subMonths(SquawkAllocationService::ALLOCATION_HISTORY_KEEP_MONTHS)
                    ->addDay(1)
                    ->toDateTimeString(),
                'new' => true,
            ]
        );

        // This one is expired
        AllocationHistory::create(
            [
                'callsign' => 'NAX456',
                'squawk' => '1423',
                'allocated_by' => UserTableSeeder::ACTIVE_USER_CID,
                'allocated_at' => Carbon::now()
                    ->subMonths(SquawkAllocationService::ALLOCATION_HISTORY_KEEP_MONTHS)
                    ->subDay(1)
                    ->toDateTimeString(),
                'new' => true,
            ]
        );
    }
}
