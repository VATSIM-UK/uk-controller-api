<?php

namespace App\Console\Commands;

use App\Models\Release\Departure\DepartureReleaseRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanDepartureReleaseRequestHistory extends Command
{
    protected $signature = 'departure-releases:clean-history';

    protected $description = 'Delete any departure release requests older than three months';

    public function handle(): int
    {
        DepartureReleaseRequest::where(
            'created_at',
            '<',
            Carbon::now()->subMonths(3)->toDateTimeString()
        )->forceDelete();
        $this->info('Departure release request history cleaned successfully');
        return 0;
    }
}
