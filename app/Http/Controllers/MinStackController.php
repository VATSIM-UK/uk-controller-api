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
     * @param MinStackLevelService $minStackLevelService
     */
    public function __construct(MinStackLevelService $minStackLevelService)
    {
        $this->minStackLevelService = $minStackLevelService;
    }

    /**
     * @return JsonResponse
     */
    public function getAirfieldMinStackLevels() : JsonResponse
    {
        return response()->json($this->minStackLevelService->getAllAirfieldMinStackLevels());
    }

    /**
     * @return JsonResponse
     */
    public function getTmaMinStackLevels() : JsonResponse
    {
        return response()->json($this->minStackLevelService->getAllTmaMinStackLevels());
    }

    /**
     * @return JsonResponse
     */
    public function getAllMinStackLevels() : JsonResponse
    {
        $return = [
            'airfield' => $this->minStackLevelService->getAllAirfieldMinStackLevels(),
            'tma' => $this->minStackLevelService->getAllTmaMinStackLevels()
        ];

        return response()->json($return);
    }

    /**
     * @param string $icao
     * @return JsonResponse
     */
    public function getMslForAirfield(string $icao) : JsonResponse
    {
        $msl = $this->minStackLevelService->getMinStackLevelForAirfield($icao);

        if ($msl === null) {
            return response()->json(null)->setStatusCode(404);
        }

        return response()->json(['msl' => $msl])->setStatusCode(200);
    }

    /**
     * @param string $tma
     * @return JsonResponse
     */
    public function getMslForTma(string $tma) : JsonResponse
    {
        $msl = $this->minStackLevelService->getMinStackLevelForTma($tma);

        if ($msl === null) {
            return response()->json(null)->setStatusCode(404);
        }

        return response()->json(['msl' => $msl])->setStatusCode(200);
    }
}
