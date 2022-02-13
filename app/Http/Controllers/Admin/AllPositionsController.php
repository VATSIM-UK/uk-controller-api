<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Controller\ControllerPosition;

class AllPositionsController extends BaseController
{
	public function __invoke()
	{
		// define fields which are superfluous for the purpose of this endpoint.
        $irrelevantFields = ['requests_departure_releases', 'receives_departure_releases', 'sends_prenotes', 'receives_prenotes'];
			  
        $positions = ControllerPosition::all()->makeHidden($irrelevantFields)->toArray();

        return response()->json(['positions' => $positions]);
	}
}
