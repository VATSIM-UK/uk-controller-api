<?php

namespace App\Console\Commands;

use App\Services\Metar\MetarService;
use Illuminate\Console\Command;

class UpdateMetars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metars:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the METAR data';

    public function handle(MetarService $metarService)
    {
        $this->info('Starting METAR update');
        $metarService->updateAllMetars();
        $this->info('METARs successfully updated');
    }
}
