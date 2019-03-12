<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Broadcast;
use Laravel\Lumen\Http\Request;

class BroadcastController extends BaseController
{
    public function authenticate(Request $request)
    {
        return Broadcast::auth($request);
    }
}
