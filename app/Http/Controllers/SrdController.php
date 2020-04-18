<?php

namespace App\Http\Controllers;

use App\Models\Srd\SrdRoute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SrdController
{
    public function searchRoutes(Request $request): JsonResponse
    {
        // Do some validation
        $rules = [
            'origin' => 'required|alpha',
            'destination' => 'required|alpha',
            'minLevel' => 'integer',
            'maxLevel' => 'integer',
        ];

        $validator = Validator::make($request->query(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 400);
        }

        // Build the query
        $query = SrdRoute::where('origin', $request->query('origin'))
            ->where('destination', $request->query('destination'));

        if ($request->query('minLevel')) {
            $query->where('min_level', '<=', $request->query('minLevel'))
                ->orWhereNull('min_level');
        }

        if ($request->query('maxLevel')) {
            $query->where('max_level', '>=', $request->query('maxLevel'));
        }

        // Format the results
        $results = $query->get()->map(function (SrdRoute $route) {
            $routeString = is_null($route->sid)
                ? $route->route_segment
                : sprintf('%s %s', $route->sid, $route->route_segment);

            return [
                'min_level' => $route->min_level,
                'max_level' => $route->max_level,
                'route_string' => $routeString,
            ];
        });

        return response()->json($results);
    }

}
