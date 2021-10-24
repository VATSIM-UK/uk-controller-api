<?php   

namespace App\Services;

use App\Models\Vatsim\NetworkControllerPosition;
use Carbon\Carbon;

class NetworkControllerService
{
    private NetworkDataService $dataService;

    public function __construct(NetworkDataService $dataService)
    {
        $this->dataService = $dataService;
    }

    public function updateNetworkData(): void
    {
        $this->updateDatabaseControllers();
        $this->processTimeouts();
    }

    private function updateDatabaseControllers(): void
    {
        $networkControllers = $this->dataService->getNetworkControllerData();
        if ($networkControllers->isEmpty()) {
            return;
        }

        NetworkControllerPosition::upsert(
            $networkControllers->map(function ($controller) {
                return [
                    'cid' => $controller['cid'],
                    'callsign' => $controller['callsign'],
                    'frequency' => $controller['frequency'],
                ];
            })->toArray(),
            ['cid'],
            ['callsign', 'cid', 'frequency']
        );
    }

    private function processTimeouts()
    {
        NetworkControllerPosition::where('updated_at', '<', Carbon::now()->subMinutes(3))
            ->delete();
    }
}
