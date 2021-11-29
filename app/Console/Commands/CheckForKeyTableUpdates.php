<?php

namespace App\Console\Commands;

use App\Services\DatabaseService;
use Illuminate\Console\Command;

class CheckForKeyTableUpdates extends Command
{
    protected $signature = 'database:check-table-updates';
    protected $description = 'Check for updates against key database tables so we can do things like bump dependencies';

    public function handle(DatabaseService $databaseService)
    {
        $this->info('Performing database table update check');
        $databaseService->updateTableStatus();
        $this->info('Table status updated');
    }
}
