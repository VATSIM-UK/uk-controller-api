<?php

namespace App\Http\Controllers;

use App\Models\Srd\SrdNote;
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
        $query = SrdRoute::with('notes')
            ->where('origin', $requestData['origin'])
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

            // Add SID fix to start of string if it's available
            $routeString = '';
            if ($route->sid) {
                $routeString .= sprintf('%s ', $route->sid);
            }

            // Add the main route segement
            $routeString .= $route->route_segment;

            /*
             * If the destination isn't an airport, append the destination as SRD routes
             * for these stop at the last airway.
             */
            if (strlen($route->destination) !== 4) {
                $routeString .= sprintf(' %s', $route->destination);
            }

            return [
                'minimum_level' => $route->minimum_level,
                'maximum_level' => $route->maximum_level,
                'route_string' => $routeString,
                'notes' => $route->notes->map(function (SrdNote $note) {
                    return [
                        'id' => $note->id,
                        'text' => $note->note_text
                    ];
                }),
            ];
        });

        return response()->json($results);
    }
}
