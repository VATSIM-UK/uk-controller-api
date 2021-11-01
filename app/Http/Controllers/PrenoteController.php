<?php

namespace App\Http\Controllers;

use App\Models\Controller\Prenote;
use App\Services\PrenoteService;
use Illuminate\Http\JsonResponse;

class PrenoteController extends Prenote
{
    private PrenoteService $prenoteService;

    public function __construct(PrenoteService $prenoteService)
    {
        $this->prenoteService = $prenoteService;
    }

    public function getPrenotesV2Dependency(): JsonResponse
    {
        return response()->json($this->prenoteService->getPrenotesV2Dependency());
    }
}
