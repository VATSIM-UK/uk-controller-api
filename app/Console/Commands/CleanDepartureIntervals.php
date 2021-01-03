<?php

namespace App\Console\Commands;

use App\Models\Departure\DepartureInterval;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanDepartureIntervals extends Command
{
    protected $signature = 'departure-intervals:clean';

    protected $description = 'Remove departure restrictions older than a specified age';

    public function handle(): int
    {
        DepartureInterval::where(
            'expires_at',
            '<',
            Carbon::now()->subMonths(3)
        )->forceDelete();
        $this->info('Departure restrictions cleaned successfully');
        return 0;
    }
}
