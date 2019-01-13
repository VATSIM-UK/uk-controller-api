<?php

namespace App\Console\Commands;

use App\Services\SquawkAllocationService;
use Illuminate\Console\Command;

class CleanSquawkAllocationHistory extends Command
{
    protected $signature = 'allocations:clean-history';

    protected $description = 'Delete any squawk audit history that is older than a specified age';

    /**
     * Handles the command
     * @param SquawkAllocationService $squawkAllocationService
     */
    public function handle(SquawkAllocationService $squawkAllocationService)
    {
        $squawkAllocationService->deleteOldAuditHistory();
        $this->info('Squawk allocation audit history cleaned successfully');
    }
}
