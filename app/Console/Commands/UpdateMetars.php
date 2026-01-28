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

        try {
            $this->info('Fetching all airfields from database');
            $startTime = microtime(true);

            $metarService->updateAllMetars();

            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);
            $this->info("METARs successfully updated in {$duration}s");
        } catch (\Exception $e) {
            $this->error("METAR update failed: {$e->getMessage()}");
            $this->error("Stack trace: {$e->getTraceAsString()}");
            throw $e;
        }
    }
}
