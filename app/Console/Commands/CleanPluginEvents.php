<?php

namespace App\Console\Commands;

use App\Models\Plugin\PluginEvent;
use App\Models\Stand\StandAssignmentsHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanPluginEvents extends Command
{
    protected $signature = 'plugin-events:clean';

    protected $description = 'Cleans any plugin events older than one hour';

    public function handle(): int
    {
        PluginEvent::where(
            'created_at',
            '<',
            Carbon::now()->subHour()
        )->delete();
        $this->info('Plugin events cleaned successfully');
        return 0;
    }
}
