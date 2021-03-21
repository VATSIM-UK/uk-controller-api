<?php
namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\JsonResponse;

/**
 * A very basic controller for simply utils connectivity and middleware.
 *
 * Class DefaultController
 *
 * @package App\Http\Controllers
 */
class TeapotController
{
    /**
     * A holding page that simply tells the user that they really need to go and
     * rethink what they're doing. Also tracks last login.
     *
     * @return Response
     */
    public function normalTeapots() : JsonResponse
    {
        return response()->json(
            [
                'message' => 'Nothing here but us teapots...',
            ]
        )->setStatusCode(200);
    }
}
