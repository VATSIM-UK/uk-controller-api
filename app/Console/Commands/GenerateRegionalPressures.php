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

    const SUCCESS_MESSAGE = 'Regional pressure settings updated successfully';
    const FAILURE_MESSAGE = 'Unable to retrieve Regional Pressure Settings';

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
            event(new RegionalPressuresUpdatedEvent($regionalPressures));
            Log::info(self::SUCCESS_MESSAGE);
            $this->info(self::SUCCESS_MESSAGE);
            return 0;
        } else {
            Log::error(self::FAILURE_MESSAGE . ': ' . $service->getLastError());
            $this->error(self::FAILURE_MESSAGE);
            return 1;
        }
    }
}
