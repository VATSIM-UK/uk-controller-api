<?php

namespace App\Console\Commands;

use App\Services\SrdService;
use Illuminate\Console\Command;

class UpdateSrd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'srd:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the SRD data automatically from the document';

    /**
     * Execute the console command.
     *
     * @throws \App\Exceptions\SrdUpdateFailedException
     */
    public function handle(SrdService $service)
    {
        $this->info('Starting SRD update');
        if (!$service->srdNeedsUpdating()) {
            $this->info('SRD is up to date');
            return;
        }

        $service->updateSrdData();
        $this->info('SRD updated successfully');
    }
}
