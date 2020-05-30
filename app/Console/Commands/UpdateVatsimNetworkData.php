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
     * @var NetworkDataService
     */
    private $dataService;

    public function __construct(NetworkDataService $dataService)
    {
        parent::__construct();
        $this->dataService = $dataService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Starting network data update');
        $this->dataService->updateNetworkData();
        $this->info('Network data successfully updated');
    }
}
