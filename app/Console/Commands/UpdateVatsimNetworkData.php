<?php

namespace App\Console\Commands;

use App\Services\NetworkAircraftService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UpdateVatsimNetworkData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'networkdata:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the VATSIM network data';

    /**
     * Execute the console command.
     *
     * @param NetworkAircraftService $dataService
     * @return mixed
     */
    public function handle(NetworkAircraftService $dataService)
    {
        $startTime = microtime(true);
        $this->info('Starting network data update');
        Log::debug('UpdateVatsimNetworkData: Starting command execution', ['timestamp' => Carbon::now()]);

        $dataService->updateNetworkData();

        $duration = microtime(true) - $startTime;
        $this->info('Network data successfully updated');
        Log::debug('UpdateVatsimNetworkData: Completed command execution', [
            'duration_seconds' => $duration,
            'timestamp' => Carbon::now()
        ]);
    }
}
