<?php

namespace App\Http\Controllers;

use App\Services\MinStackLevelService;
use Illuminate\Http\JsonResponse;

class MinStackController extends BaseController
{
    /**
     * @var MinStackLevelService
     */
    private $minStackLevelService;

    /**
     * MinStackController constructor.
     */
    public function __construct(MinStackLevelService $minStackLevelService)
    {
        $this->minStackLevelService = $minStackLevelService;
    }

    public function getAirfieldMinStackLevels(): JsonResponse
    {
        return response()->json($this->minStackLevelService->getAllAirfieldMinStackLevels());
    }

    public function getTmaMinStackLevels(): JsonResponse
    {
        return response()->json($this->minStackLevelService->getAllTmaMinStackLevels());
    }

    public function getAllMinStackLevels(): JsonResponse
    {
        $return = [
            'airfield' => $this->minStackLevelService->getAllAirfieldMinStackLevels(),
            'tma' => $this->minStackLevelService->getAllTmaMinStackLevels(),
        ];

        return response()->json($return);
    }

    public function getMslForAirfield(string $icao): JsonResponse
    {
        $msl = $this->minStackLevelService->getMinStackLevelForAirfield($icao);

        if ($msl === null) {
            return response()->json(null)->setStatusCode(404);
        }

        return response()->json(['msl' => $msl])->setStatusCode(200);
    }

    public function getMslForTma(string $tma): JsonResponse
    {
        $msl = $this->minStackLevelService->getMinStackLevelForTma($tma);

        if ($msl === null) {
            return response()->json(null)->setStatusCode(404);
        }

        return response()->json(['msl' => $msl])->setStatusCode(200);
    }
}
