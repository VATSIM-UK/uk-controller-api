<?php
namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Models\Squawks\Allocation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

class CleanSquawkAllocationsTest extends BaseFunctionalTestCase
{
    public function testItConstructs()
    {
        $this->assertInstanceOf(CleanSquawkAllocations::class, $this->app->make(CleanSquawkAllocations::class));
    }

    public function testItCleansAllocations()
    {
        // Add some allocations
        Allocation::create(
            [
                'callsign' => 'VIR25E',
                'squawk' => '2321',
                'allocated_by' => self::ACTIVE_USER_CID,
                'allocated_at' => Carbon::now()->subMinutes(env('APP_SQUAWK_ALLOCATION_MIN') - 1),
            ]
        );
        Allocation::create(
            [
                'callsign' => 'UAL242',
                'squawk' => '4325',
                'allocated_by' => self::ACTIVE_USER_CID,
                'allocated_at' => Carbon::now()->subMinutes(env('APP_SQUAWK_ALLOCATION_MIN') + 10),
            ]
        );
        Allocation::create(
            [
                'callsign' => 'TCX125',
                'squawk' => '5436',
                'allocated_by' => self::ACTIVE_USER_CID,
                'allocated_at' => Carbon::now()->subMinutes(env('APP_SQUAWK_ALLOCATION_MIN') + 1),
            ]
        );
        
        $this->seeInDatabase('squawk_allocation', ['callsign' => 'VIR25E']);
        $this->seeInDatabase('squawk_allocation', ['callsign' => 'UAL242']);
        $this->seeInDatabase('squawk_allocation', ['callsign' => 'TCX125']);
        Artisan::call('allocations:clean');

        // We should lose the allocations that are too old
        $this->seeInDatabase('squawk_allocation', ['callsign' => 'VIR25E']);
        $this->notSeeInDatabase('squawk_allocation', ['callsign' => 'UAL242']);
        $this->notSeeInDatabase('squawk_allocation', ['callsign' => 'TCX125']);
    }
}
