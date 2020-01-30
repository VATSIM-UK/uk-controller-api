<?php

namespace App\Http\Controllers;

use App\Models\Controller\Prenote;
use App\Services\PrenoteService;
use Illuminate\Http\JsonResponse;

class PrenoteController extends Prenote
{
    /**
     * @var PrenoteService
     */
    private $prenoteService;

    /**
     * PrenoteController constructor.
     * @param PrenoteService $prenoteService
     */
    public function __construct(PrenoteService $prenoteService)
    {
        $this->prenoteService = $prenoteService;
    }

    public function getAllPrenotes() : JsonResponse
    {
        return response()->json($this->prenoteService->getAllPrenotesWithControllers());
    }
}
