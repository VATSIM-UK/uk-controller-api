<?php
namespace App\Http\Controllers;

use App\Exceptions\SquawkNotAllocatedException;
use App\Exceptions\SquawkNotAssignedException;
use App\Services\SquawkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Handles requests for the allocation and deallocation of squawks.
 *
 * Class SquawkController
 *
 * @package App\Http\Controllers
 */
class SquawkController extends BaseController
{
    /**
     * The squawk to assign if something goes wrong.
     *
     * @var String
     */
    const FAILURE_SQUAWK = '7000';

    // Message prefix for successful squawk de-allocation
    const DEALLOCATE_SUCCESS_PREFIX = 'Squawk successfully de-allocated for ';

    // Message prefix for unsuccessful squawk de-allocation.
    const DEALLOCATE_FAILURE_PREFIX =  'Squawk de-allocation unsuccessful for ';

    /**
     * Service for allocating squawks
     *
     * @var SquawkService
     *
     */
    private $squawkService;

    /**
     * @param SquawkService $squawkService
     */
    public function __construct(SquawkService $squawkService)
    {
        $this->squawkService = $squawkService;
    }


    /**
     * Returns the squawk assigned to the given callsign.
     *
     * @param string $callsign Callsign to check
     * @return JsonResponse
     */
    public function getSquawkAssignment(string $callsign) : JsonResponse
    {
        $assignment = $this->squawkService->getAssignedSquawk($callsign);
        if (!$assignment) {
            return response()->json(
                [
                    'message' => 'Assignment not found for BAW12AZ',
                ]
            )->setStatusCode(404);
        }

        return response()->json(
            [
                'squawk' => $assignment->getCode(),
            ]
        )->setStatusCode(200);
    }

    /**
     * Works out what type of assignment to do and defers to
     * the appropriate method.
     *
     * @param Request $request Request object
     * @param string $callsign The callsign to assign for
     * @return JsonResponse
     */
    public function assignSquawk(Request $request, string $callsign) : JsonResponse
    {
        // Check that we have a valid squawk type
        $typeCheck = $this->checkForSuppliedData(
            $request,
            [
                'type' => 'required|in:general,local',
            ]
        );

        if ($typeCheck) {
            return $typeCheck;
        }

        return $request->json('type') === 'general'
            ? $this->assignGeneralSquawk($request, $callsign) :
            $this->assignLocalSquawk($request, $callsign);
    }

    /**
     * Gets a general squawk to use for a particular callsign.
     *
     * @param Request $request The HTTP Request Object
     * @param string $callsign The callsign to allocate the squawk to
     * @return JsonResponse
     */
    private function assignGeneralSquawk(Request $request, string $callsign) : JsonResponse
    {
        // Missing data check
        $check = $this->checkForSuppliedData(
            $request,
            [
                'origin' => 'required|alpha|size:4',
                'destination' => 'required|alpha|size:4',
            ]
        );

        if ($check) {
            return $check;
        }

        $assignment = $this->squawkService->assignGeneralSquawk(
            $callsign,
            $request->json('origin'),
            $request->json('destination')
        );

        if (!$assignment)  {
            Log::error(
                'Unable to allocate general squawk for ' . $callsign,
                $request->json()->all()
            );
            return response()->json(
                [
                    'message' => 'Unable to allocate general squawk for ' . $callsign,
                    'squawk' => self::FAILURE_SQUAWK,
                ]
            )->setStatusCode(500);
        }

        return response()->json(
            [
                'squawk' => $assignment->getCode(),
            ]
        )->setStatusCode(201);
    }

    /**
     * Gets a squawk local to a particular ATC unit or airfield.
     *
     * @param Request $request Request object
     * @param string $callsign The callsign to allocate the squawk to
     * @return JsonResponse
     */
    public function assignLocalSquawk(Request $request, string $callsign) : JsonResponse
    {
        // Missing data check
        $check = $this->checkForSuppliedData(
            $request,
            [
                'unit' => 'required|alpha',
                'rules' => 'required|in:V,I,S',
            ]
        );

        if ($check) {
            return $check;
        }

        $squawk = null;

        // Get the squawk
        $assignment = $this->squawkService->assignLocalSquawk(
            $callsign,
            $request->json('unit'),
            $request->json('rules')
        );
        if (!$assignment) {
            Log::error(
                'Unable to allocate local squawk for ' . $callsign,
                $request->json()->all()
            );
            return response()->json(
                [
                    'message' => 'Unable to allocate local squawk for ' . $callsign,
                    'squawk' => self::FAILURE_SQUAWK,
                ]
            )->setStatusCode(500);
        }

        return response()->json(
            [
                'squawk' => $assignment->getCode(),
            ]
        )->setStatusCode(201);
    }

    /**
     * De-allocates a given squawk from a given aircraft.
     *
     * @param string $callsign The callsign to deallocate for
     * @param SquawkService $squawkService Service for squawk things.
     * @return Response
     */
    public function deleteSquawkAssignment(string $callsign, SquawkService $squawkService) : Response
    {
        $squawkService->deleteSquawkAssignment($callsign);
        return response('', 204);
    }
}
