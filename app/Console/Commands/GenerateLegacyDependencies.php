<?php

namespace App\Console\Commands;

use App\Services\ControllerService;
use App\Services\SidService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
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
    public function handle(SidService $sidService, ControllerService $controllerService)
    {
        $this->info('Generating legacy dependencies');
        $this->putDependency('initial-altitudes.json', $sidService->getInitialAltitudeDependency());
        $this->putDependency('controller-positions.json', $controllerService->getLegacyControllerPositionsDependency());
        $this->putDependency('airfield-ownership.json', $controllerService->getLegacyAirfieldOwnershipDependency());
        Artisan::call('cache:clear');
        $this->info('Done!');
        return 0;
    }

    private function putDependency(string $dependencyFile, $data)
    {
        $this->info('Creating legacy dependency file ' . $dependencyFile);
        Storage::disk('local')
            ->put(
                self::DEPENDENCY_ROOT . '/' . $dependencyFile,
                json_encode($data)
            );
    }
}
