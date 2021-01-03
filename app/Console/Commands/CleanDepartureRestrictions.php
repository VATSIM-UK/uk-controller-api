<?php

namespace App\Console\Commands;

use App\Models\Departure\DepartureRestriction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanDepartureRestrictions extends Command
{
    protected $signature = 'departure-restrictions:clean';

    protected $description = 'Remove departure restrictions older than a specified age';

    public function handle(): int
    {
        DepartureRestriction::where(
            'expires_at',
            '<',
            Carbon::now()->subMonths(3)
        )->forceDelete();
        $this->info('Departure restrictions cleaned successfully');
        return 0;
    }
}
