<?php

namespace App\Http\Controllers;

use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class StandController
{
    public function getStandsDependency(): JsonResponse
    {
        $stands = Stand::all()->groupBy('airfield_id')->mapWithKeys(function (Collection $collection) {
            return [
                Airfield::find($collection->first()->airfield_id)->code => $collection->map(function (Stand $stand) {
                    return [
                        'id' => $stand->id,
                        'identifier' => $stand->identifier
                    ];
                }),
            ];
        });

        return response()->json($stands);
    }
}
