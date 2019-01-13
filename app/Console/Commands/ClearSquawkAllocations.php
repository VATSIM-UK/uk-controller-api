<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Command to clear all squawk allocations.
 *
 * Class ClearSquawkAllocations
 * @package App\Console\Commands
 */
class ClearSquawkAllocations extends Command
{
    protected $signature = 'allocations:clear';

    protected $description = 'Remove all squawk allocations';

    public function handle()
    {
        // Clear the database of all squawk allocations
        DB::table('squawk_allocation')->delete();
        Log::info('All squawk allocations deleted successfully');
        $this->info('All squawk allocations deleted successfully');
    }
}
