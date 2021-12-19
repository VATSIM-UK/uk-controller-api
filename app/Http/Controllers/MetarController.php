<?php

namespace App\Http\Controllers;

use App\Models\Metars\Metar;
use Illuminate\Http\JsonResponse;

class MetarController
{
    public function getAllMetars(): JsonResponse
    {
        return response()->json(
            Metar::with('airfield')->get()->map(function (Metar $metar) {
                return [
                    'airfield_id' => $metar->airfield_id,
                    'raw' => $metar->raw,
                    'parsed' => $metar->parsed,
                ];
            })
        );
    }
}
