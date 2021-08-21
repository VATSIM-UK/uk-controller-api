<?php

namespace App\Console\Commands;

use App\Models\Acars\AcarsMessage;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanAcarsMessages extends Command
{
    protected $signature = 'acars:clean-history';

    protected $description = 'Delete any acars message history that is older than one month';

    public function handle(): int
    {
        AcarsMessage::where(
            'created_at',
            '<',
            Carbon::now()->subMonth()
        )->forceDelete();
        $this->info('Acars message history cleaned successfully');
        return 0;
    }
}
