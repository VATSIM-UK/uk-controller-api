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
        $navaid = Navaid::create($request->validated());
        return response()->json(['identifier' => $navaid->identifier], 201);
    }

    /**
     * Modify the existing navaid.
     *
     * @param Navaid $navaid
     * @param NavaidRequest $request
     */
    public function modifyNavaid(Navaid $navaid, NavaidRequest $request): JsonResponse
    {
        $navaid->update($request->validated());
        return response()->json(['identifier' => $navaid->identifier]);
    }

    /**
     * Delete the Navaid
     *
     * @param Navaid $navaid
     */
    public function deleteNavaid(Navaid $navaid): JsonResponse
    {
        $navaid->delete();
        return response()->json([], 204);
    }
}
