<?php
namespace App\Console\Commands;

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
    protected $signature = 'regionals:generate';

    protected $description = 'Regenerate the regional pressures file';

    const RPS_CACHE_KEY = 'regional_pressures';

    /**
     * Generates regional pressures and logs the response
     *
     * @param RegionalPressureService $service Service for generation
     */
    public function handle(RegionalPressureService $service)
    {
        if ($service->generateRegionalPressures()) {
            Log::info('Regional pressure settings updated and cached successfully.');
            $this->info('Regional pressure settings updated and cached successfully.');
            return 0;
        } else {
            Log::error('Unable to retrieve Regional Pressure Settings.');
            $this->error('Unable to retrieve Regional Pressure Settings.');
            return 1;
        }
    }
}
