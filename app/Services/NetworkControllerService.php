<?php

namespace App\Services;

use App\Events\NetworkControllersUpdatedEvent;
use App\Helpers\Vatsim\ControllerPositionParser;
use App\Models\Vatsim\NetworkControllerPosition;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NetworkControllerService
{
    private NetworkDataService $dataService;
    private ControllerPositionParser $positionParser;
    private ControllerService $controllerService;

    public function __construct(
        NetworkDataService $dataService,
        ControllerService $controllerService,
        ControllerPositionParser $positionParser
    ) {
        $this->dataService = $dataService;
        $this->controllerService = $controllerService;
        $this->positionParser = $positionParser;
    }

    public function updateNetworkData(): void
    {
        $startTime = microtime(true);
        Log::debug('NetworkControllerService: Starting network data update');

        $this->updateDatabaseControllers();
        Log::debug('NetworkControllerService: Updated database controllers');

        $this->processTimeouts();
        Log::debug('NetworkControllerService: Processed controller timeouts');

        event(new NetworkControllersUpdatedEvent());

        $duration = microtime(true) - $startTime;
        Log::debug('NetworkControllerService: Completed network data update', [
            'duration_seconds' => $duration
        ]);
    }

    private function updateDatabaseControllers(): void
    {
        $networkControllers = $this->dataService->getNetworkControllerData();
        if ($networkControllers->isEmpty()) {
            Log::debug('NetworkControllerService: No network controller data received');
            return;
        }

        Log::debug('NetworkControllerService: Upserting network controllers', ['count' => $networkControllers->count()]);

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

    private function processTimeouts(): void
    {
        $timedOutControllers = NetworkControllerPosition::where('updated_at', '<', Carbon::now()->subMinutes(3))->count();
        if ($timedOutControllers > 0) {
            Log::debug('NetworkControllerService: Removing timed-out controllers', ['count' => $timedOutControllers]);
        }

        NetworkControllerPosition::where('updated_at', '<', Carbon::now()->subMinutes(3))
            ->delete();
    }

    public function updatedMatchedControllerPositions(): void
    {
        $recognisedPositions = $this->controllerService->getParsedControllerPositionsWithAlternatives();
        NetworkControllerPosition::all()->each(function (NetworkControllerPosition $position) use ($recognisedPositions) {
            $parsedPosition = $this->positionParser->parse($position);
            if (!$parsedPosition) {
                $position->clearActiveControllerPosition();
                return;
            }

            $matchedPosition = $recognisedPositions->search(function (Collection $positions) use ($parsedPosition) {
                return $positions->contains($parsedPosition);
            });
            if ($matchedPosition === false) {
                $position->clearActiveControllerPosition();
                return;
            }

            $position->setActiveControllerPosition($matchedPosition);
        });
    }
}
