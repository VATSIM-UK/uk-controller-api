<?php

namespace App\Http\Controllers;

use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class AircraftController extends BaseController
{
    public function getAllAircraft() : JsonResponse
    {
        return response()->json(Aircraft::all());
    }

    public function getAllWakeCategories()
    {
        return response()->json(WakeCategory::all());
    }

    public function getWakeCategoriesDependency(): JsonResponse
    {
        return response()->json($this->getWakeDependency('uk'));
    }

    public function getRecatCategoriesDependency(): JsonResponse
    {
        return response()->json($this->getWakeDependency('recat'));
    }

    private function getWakeDependency(string $schemeName): array
    {
        return Aircraft::with('wakeCategories', 'wakeCategories.scheme')->whereHas(
            'wakeCategories',
            function (Builder $wakeCategory) use ($schemeName) {
                return $wakeCategory->whereHas(
                    'scheme',
                    function (Builder $scheme) use ($schemeName) {
                        return $scheme->$schemeName();
                    }
                );
            }
        )->get()->mapWithKeys(
            function (Aircraft $aircraft) use ($schemeName) {
                return [
                    $aircraft->code => $aircraft->wakeCategories()->whereHas(
                        'scheme',
                        function (Builder $scheme) use ($schemeName) {
                            return $scheme->$schemeName();
                        }
                    )->first()->code
                ];
            }
        )->toArray();
    }
}
