<?php
namespace App\Console\Commands;

use App\Events\RegionalPressuresUpdatedEvent;
use App\Services\RegionalPressureService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Command to generate all the regional pressure settings.
 *
 * Class GenerateRegionalPressures
 * @package App\Console\Commands
 */
class GenerateRegionalPressures extends Command
{
    protected $signature = 'regional:generate';

    protected $description = 'Regenerate the regional pressure settings';

    /**
     * Generates regional pressures and logs the response
     *
     * @param RegionalPressureService $service Service for generation
     * @return int
     */
    public function handle(RegionalPressureService $service)
    {
        $this->info('Generating regional pressure settings');
        $regionalPressures = $service->generateRegionalPressures();
        if (!is_null($regionalPressures) && count($regionalPressures) !== 0) {
            Log::info('Regional pressure settings updated successfully.');
            $this->info('Regional pressure settings updated successfully.');
            return 0;
        } else {
            Log::error('Unable to retrieve Regional Pressure Settings: ' . $service->getLastError());
            $this->error('Unable to retrieve Regional Pressure Settings.');
            return 1;
        }
    }
}
