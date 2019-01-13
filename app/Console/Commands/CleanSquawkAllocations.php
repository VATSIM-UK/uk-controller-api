<?php
namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Command for cleaning all squawk allocations that are older than the specified age.
 *
 * Class CleanSquawkAllocations
 * @package App\Console\Commands
 */
class CleanSquawkAllocations extends Command
{
    protected $signature = 'allocations:clean';

    protected $description = 'Remove any squawk allocations that have passed their expiry time';

    public function handle()
    {
        // Delete anything that is older than the cutoff
        DB::table('squawk_allocation')->where(
            'allocated_at',
            '<',
            Carbon::now()->subMinutes(env('APP_SQUAWK_ALLOCATION_MIN'))->format('Y-m-d H:i:s')
        )->delete();

        Log::Info('Squawk allocations cleaned successfully');
        $this->info('Squawk allocations cleaned successfully');
    }
}
