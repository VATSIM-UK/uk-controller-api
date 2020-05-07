<?php

namespace App\Http\Controllers;

use App\Models\Navigation\Navaid;
use Illuminate\Http\JsonResponse;

class NavaidController
{
    public function getNavaidsDependency(): JsonResponse
    {
        $navaids = Navaid::all()->each(function (Navaid $navaid) {
            $navaid->setHidden(['created_at', 'updated_at']);
        });
        return response()->json($navaids);
    }
}
