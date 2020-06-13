<?php

namespace App\Http\Controllers;

use App\Models\Navigation\Navaid;
use Illuminate\Http\JsonResponse;

class NavaidController extends BaseController
{
    public function __invoke(): JsonResponse
    {
        $navaids = Navaid::all()->each(function (Navaid $navaid) {
            $navaid->setHidden(['created_at', 'updated_at']);
        });
        return response()->json($navaids);
    }
}
