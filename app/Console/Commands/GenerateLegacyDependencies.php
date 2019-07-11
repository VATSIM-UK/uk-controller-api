<?php

namespace App\Console\Commands;

use App\Services\SidService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Command that deletes all user tokens
 */
class GenerateLegacyDependencies extends Command
{
    protected $signature = 'dependencies:generate-legacy';

    protected $description = 'Create the legacy dependency files from new data';

    const DEPENDENCY_ROOT = 'public/dependencies';

    /**
     * Run the command
     *
     * @param SidService $sidService
     * @return integer
     */
    public function handle(SidService $sidService)
    {
        $this->info('Dumping initial altitude dependency');
        Storage::disk('local')
            ->put(
                self::DEPENDENCY_ROOT . '/initial-altitudes.json',
                json_encode($sidService->getInitialAltitudeDependency())
            );
        $this->info('Done!');
        return 0;
    }
}
