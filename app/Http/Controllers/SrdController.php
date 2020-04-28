<?php

namespace App\Http\Controllers;

use App\Models\Srd\SrdRoute;
use Illuminate\Database\Eloquent\Builder;
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
            'requestedLevel' => 'integer',
        ];

        $validator = Validator::make($request->query(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 400);
        }

        // Get the validated data
        $requestData = $validator->validated();

        // Build the query
        $query = SrdRoute::where('origin', $requestData['origin'])
            ->where('destination', $requestData['destination']);

        if (isset($requestData['requestedLevel'])) {
            $query->where(function (Builder $query) use ($requestData) {
                $query->where('minimum_level', '<=', $requestData['requestedLevel'])
                    ->orWhereNull('minimum_level');
            })
                ->where('maximum_level', '>=', $requestData['requestedLevel']);
        }

        // Format the results
        $results = $query->get()->map(function (SrdRoute $route) {
            $routeString = is_null($route->sid)
                ? $route->route_segment
                : sprintf('%s %s', $route->sid, $route->route_segment);

            $notesArray = $route->notes()->pluck('note_text', 'id');

            return [
                'minimum_level' => $route->minimum_level,
                'maximum_level' => $route->maximum_level,
                'route_string' => $routeString,
                'notes' => $notesArray,
            ];
        });

        return response()->json($results);
    }
}
