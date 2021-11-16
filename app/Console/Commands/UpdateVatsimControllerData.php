<?php

namespace App\Console\Commands;

use App\Services\NetworkControllerService;
use Illuminate\Console\Command;

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
        $this->info('Starting network controller data update');
        $dataService->updateNetworkData();
        $this->info('Network controller data successfully updated');
    }
}
