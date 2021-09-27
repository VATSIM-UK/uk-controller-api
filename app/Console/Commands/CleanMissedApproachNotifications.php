<?php

namespace App\Console\Commands;

use App\Models\MissedApproach\MissedApproachNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanMissedApproachNotifications extends Command
{
    protected $signature = 'missed-approaches:clean-history';

    protected $description = 'Delete any missed approaches older than three months';

    public function handle(): int
    {
        MissedApproachNotification::where(
            'created_at',
            '<',
            Carbon::now()->subMonths(3)->toDateTimeString()
        )->delete();
        $this->info('Missed approach history cleaned successfully');
        return 0;
    }
}
