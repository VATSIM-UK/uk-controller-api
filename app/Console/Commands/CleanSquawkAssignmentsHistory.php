<?php

namespace App\Console\Commands;

use App\Models\Squawk\SquawkAssignmentsHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanSquawkAssignmentsHistory extends Command
{
    protected $signature = 'squawks:clean-history';

    protected $description = 'Delete any squawk assignment history that is older than a specified age';

    public function handle()
    {
        SquawkAssignmentsHistory::where(
            'allocated_at',
            '<',
            Carbon::now()->subMonths(3)->toDateTimeString()
        )->forceDelete();
        $this->info('Squawk assignment audit history cleaned successfully');
    }
}
