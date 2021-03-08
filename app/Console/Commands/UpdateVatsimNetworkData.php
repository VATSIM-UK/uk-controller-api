<?php

namespace App\Console\Commands;

use App\Services\NetworkDataService;
use Illuminate\Console\Command;

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
     * @param NetworkDataService $dataService
     * @return mixed
     */
    public function handle(NetworkDataService $dataService)
    {
        $this->info('Starting network data update');
        $dataService->updateNetworkData();
        $this->info('Network data successfully updated');
    }
}
