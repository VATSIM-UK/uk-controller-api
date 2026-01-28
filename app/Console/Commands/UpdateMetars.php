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
        try {
            $this->info('Starting METAR update');
            $startTime = microtime(true);

            $metarService->updateAllMetars();

            $duration = round(microtime(true) - $startTime, 2);
            $this->info("METAR update completed in {$duration}s");
        } catch (\Exception $e) {
            $this->error("METAR update failed: {$e->getMessage()}");
            throw $e;
        }
    }
}
