<?php

namespace App\Http\Controllers;

use App\Models\Navigation\Navaid;
use App\Services\SectorfileService;
use Illuminate\Http\JsonResponse;

class NavaidController extends BaseController
{
    public function __invoke(): JsonResponse
    {
        $navaids = Navaid::all()->map(function (Navaid $navaid) {
            return [
                'id' => $navaid->id,
                'identifier' => $navaid->identifier,
                'latitude' => SectorfileService::convertLatitudeToSectorfileFormat($navaid->latitude),
                'longitude' => SectorfileService::convertLongitudeToSectorfileFormat($navaid->longitude),
            ];
        });
        return response()->json($navaids);
    }
}
