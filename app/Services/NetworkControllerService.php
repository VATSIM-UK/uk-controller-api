<?php

namespace App\Services;

use App\Events\NetworkControllersUpdatedEvent;
use App\Helpers\Vatsim\ControllerPositionParser;
use App\Models\Vatsim\NetworkControllerPosition;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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
        $this->updateDatabaseControllers();
        $this->processTimeouts();
        event(new NetworkControllersUpdatedEvent());
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

    private function processTimeouts(): void
    {
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
