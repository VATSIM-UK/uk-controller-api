<?php

namespace App\Http\Controllers\Admin;

use App\Models\Hold\Hold;
use App\Models\Navigation\Navaid;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\HoldRequest;
use App\Services\DependencyService;
use App\Http\Controllers\BaseController;

class HoldAdminController extends BaseController
{
    private const DESCRIPTION_NOT_UNIQUE_RESPONSE = ['message' => 'Description of hold already used.'];
    private const HOLD_NOT_IN_NAVAID_RESPONSE = ['message' => 'Hold not associated with Navaid.'];
    /**
     * Get a list of holds for a specified Navaid.
     *
     * @param Navaid $navaid
     * @return JsonResponse
     */
    public function getHolds(Navaid $navaid) : JsonResponse
    {
        $holds = $navaid->load(['holds'])->holds;

        // load the restrictions relation of the holds
        $holds->load(['restrictions']);

        return response()->json(['holds' => $holds]);
    }

    /**
     * Create a new hold attached to a specified Navaid.
     *
     * @param Navaid $navaid
     * @param HoldRequest $request
     * @return JsonResponse
     */
    public function createHold(Navaid $navaid, HoldRequest $request) : JsonResponse
    {
        // use description if specified, otherwise default to the navaid identifier.
        $description = $request->get('description') ?? $navaid->identifier;

        if ($this->doesDescriptionAlreadyExist($navaid, $description)) {
            return response()->json(self::DESCRIPTION_NOT_UNIQUE_RESPONSE, 409);
        }

        $hold = $navaid->holds()->create(
            array_merge($request->validated(), compact('description'))
        );

        return response()->json(['hold_id' => $hold->id], 201);
    }

    /**
     * Get the details of a hold associated with a Navaid.
     *
     * @param Navaid $navaid
     * @param Hold $hold
     * @return JsonResponse
     */
    public function getHold(Navaid $navaid, Hold $hold) : JsonResponse
    {
        if (! $this->checkHoldBelongsToNavaid($navaid, $hold)) {
            return response()->json(self::HOLD_NOT_IN_NAVAID_RESPONSE, 404);
        }
        return response()->json(['hold' => $hold->load('restrictions')]);
    }

    /**
     * Modify an existing hold on a specified Navaid.
     *
     * @param Navaid $navaid
     * @param Hold $hold
     * @param HoldRequest $request
     * @return JsonResponse
     */
    public function modifyHold(Navaid $navaid, Hold $hold, HoldRequest $request) : JsonResponse
    {
        if (! $this->checkHoldBelongsToNavaid($navaid, $hold)) {
            return response()->json(self::HOLD_NOT_IN_NAVAID_RESPONSE, 404);
        }
    
        // use description if specified, otherwise default to the navaid identifier.
        $description = $request->get('description') ?? $navaid->identifier;

        $descriptionChanged = ($hold->description != $request->get('description'));
        if ($this->doesDescriptionAlreadyExist($navaid, $description) && $descriptionChanged) {
            return response()->json(self::DESCRIPTION_NOT_UNIQUE_RESPONSE, 409);
        }

        $hold->update($request->validated());
 
        return response()->json([], 204);
    }

    /**
     * Delete a Hold associated with a specified Navaid.
     *
     * @param Navaid $navaid
     * @param Hold $hold
     * @return JsonResponse
     */
    public function deleteHold(Navaid $navaid, Hold $hold) : JsonResponse
    {
        if (! $this->checkHoldBelongsToNavaid($navaid, $hold)) {
            return response()->json(self::HOLD_NOT_IN_NAVAID_RESPONSE, 404);
        }
        $hold->delete();
        return response()->json([], 204);
    }

    /**
     * Checks whether the description is already used for
     * the holds of a specified navaid.
     *
     * @param Navaid $navaid
     * @param string $description
     * @return boolean
     */
    private function doesDescriptionAlreadyExist(Navaid $navaid, string $description): bool
    {
        $existingDescriptions = $navaid->holds->pluck('description');

        return $existingDescriptions->contains($description);
    }

    /**
     * Checks whether the given Hold has the parent
     * of the specified Navaid.
     *
     * @param Navaid $navaid
     * @param Hold $hold
     * @return boolean
     */
    private function checkHoldBelongsToNavaid(Navaid $navaid, Hold $hold): bool
    {
        return $navaid->id == $hold->navaid_id;
    }
}
