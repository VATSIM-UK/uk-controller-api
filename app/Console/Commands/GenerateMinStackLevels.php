<?php

namespace App\Console\Commands;

use App\Events\MinStacksUpdatedEvent;
use App\Services\MinStackLevelService;
use Illuminate\Console\Command;

class GenerateMinStackLevels extends Command
{
    protected $signature = 'msl:generate';

    protected $description = 'Regenerate the minimum stack levels for TMAs and Airfields';

    /**
     * Update the minimum stack levels
     *
     * @param MinStackLevelService $service
     * @return int
     */
    public function handle(MinStackLevelService $service) : int
    {
        $this->info('Updating minimum stack levels');
        $airfields =$service->updateAirfieldMinStackLevelsFromVatsimMetarServer();
        $this->info('Successfully updated minimum stack levels for airfields');
        $tmas = $service->updateTmaMinStackLevelsFromVatsimMetarServer();
        $this->info('Successfully updated minimum stack levels for TMAs');
        event(new MinStacksUpdatedEvent($airfields, $tmas));
        return 0;
    }
}
