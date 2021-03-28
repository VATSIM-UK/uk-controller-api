<?php

namespace App\Http\Controllers\Admin;

use InvalidArgumentException;
use App\Models\Navigation\Navaid;
use Illuminate\Http\JsonResponse;
use App\Services\SectorfileService;
use App\Http\Requests\NavaidRequest;
use App\Http\Controllers\BaseController;

class NavaidAdminController extends BaseController
{
    /**
     * Get a list of all Navaids.
     *
     * @return JsonResponse
     */
    public function getNavaids() : JsonResponse
    {
        return response()->json(['navaids' => Navaid::withCount(['holds'])->get()]);
    }

    /**
     * Get the details of a given navaid, with their holds.
     *
     * @param Navaid $navaid
     * @return JsonResponse
     */
    public function getNavaid(Navaid $navaid) : JsonResponse
    {
        $navaid->load(['holds']);

        return response()->json(['navaid' => $navaid]);
    }

    /**
     * Create a new Navaid.
     *
     * @param NavaidRequest $request
     * @return JsonResponse
     */
    public function createNavaid(NavaidRequest $request) : JsonResponse
    {
        $error = $this->validateLatLongValues($request);

        if (isset($error)) {
            return response()->json(['message' => $error], 400);
        }

        $navaid = Navaid::create($request->validated());

        return response()->json(['identifier' => $navaid->identifier], 201);
    }

    public function modifyNavaid(Navaid $navaid, NavaidRequest $request)
    {
        $error = $this->validateLatLongValues($request);

        if (isset($error)) {
            return response()->json(['message' => $error], 400);
        }

        $navaid->update($request->validated());

        return response()->json(['identifier' => $navaid->identifier]);
    }

    public function deleteNavaid(Navaid $navaid)
    {
        $navaid->delete();
        
        return response()->json([], 204);
    }

    private function validateLatLongValues(NavaidRequest &$request) : ?string
    {
        try {
            SectorfileService::coordinateFromSectorfile($request->get('latitude'), $request->get('longitude'));
        } catch (InvalidArgumentException $e) {
            // exceptions should only be scoped to issues with values - format of string dealt
            // with in form request
            return $e->getMessage();
        }

        return null;
    }
}
