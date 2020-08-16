<?php

namespace App\Console\Commands;

use App\Models\Stand\StandAssignmentsHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanStandAssignmentsHistory extends Command
{
    protected $signature = 'stands:clean-history';

    protected $description = 'Delete any stand assignment history that is older than a specified age';

    public function handle(): int
    {
        StandAssignmentsHistory::where(
            'assigned_at',
            '<',
            Carbon::now()->subMonths(3)->toDateTimeString()
        )->forceDelete();
        $this->info('Stand assignment audit history cleaned successfully');
        return 0;
    }
}
