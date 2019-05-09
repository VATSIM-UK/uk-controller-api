<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller as LumenController;

/**
 * Our base controller.
 *
 * Class BaseController
 * @package App\Http\Controllers
 */
class BaseController extends LumenController
{

    /**
     *    Checks the request to validate specific rules.
     *
     * @param  Request $request  The request
     * @param  array   $rules    The laravel validation rules to match
     * @param  array   $messages (optional) The error messages to log for each rule
     * @return JsonResponse|bool
     */
    protected function checkForSuppliedData(Request $request, $rules, $messages = [])
    {
        $validator = Validator::make($request->json()->all(), $rules, $messages);

        if ($validator->fails()) {
            Log::error(
                $request->path().' requested, but some data was not provided',
                ['cid' => $request->user()->id, 'errors' => $validator->errors()->all()]
            );

            // Output first error to user
            return response()->json(
                [
                    'message' => 'Request is missing required data',
                ]
            )->setStatusCode(400, 'Request is missing required data');
        }

        return false;
    }
}
