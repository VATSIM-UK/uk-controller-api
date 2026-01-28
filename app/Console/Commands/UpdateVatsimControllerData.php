<?php

namespace App\Console\Commands;

use App\Services\NetworkControllerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UpdateVatsimControllerData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'networkdata:update-controllers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the VATSIM network controller data';


    public function handle(NetworkControllerService $dataService)
    {
        $startTime = microtime(true);
        $this->info('Starting network controller data update');
        Log::debug('UpdateVatsimControllerData: Starting command execution', ['timestamp' => Carbon::now()]);

        $dataService->updateNetworkData();

        $duration = microtime(true) - $startTime;
        $this->info('Network controller data successfully updated');
        Log::debug('UpdateVatsimControllerData: Completed command execution', [
            'duration_seconds' => $duration,
            'timestamp' => Carbon::now()
        ]);
    }
}
