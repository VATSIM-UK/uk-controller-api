<?php

namespace App\Http\Controllers;

use App\Events\PluginErrorReceivedEvent;
use App\Models\PluginError\PluginError;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

class PluginErrorController extends BaseController
{
    /**
     * Records a plugin error
     *
     * @return Response
     */
    public function recordPluginError(Request $request) : Response
    {
        $dataCheck = $this->checkForSuppliedData(
            $request,
            [
                'user_report' => 'required|boolean',
                'data' => 'required|array',
            ]
        );

        if ($dataCheck) {
            return response(null, 400);
        }

        $pluginError = PluginError::create(
            [
                'user_id' => Auth::user()->id,
                'user_report' => $request->json('user_report'),
                'data' => json_encode($request->json('data')),
            ]
        );

        Event::fire(new PluginErrorReceivedEvent($pluginError));
        return response('', 204);
    }
}
